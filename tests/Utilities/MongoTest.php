<?php

namespace Aol\Transformers\Tests\Utilities;

use Aol\Transformers\Transformer;
use Aol\Transformers\Utilities\MongoTrait;

class MongoTest extends \PHPUnit_Framework_TestCase
{
	/** @var MongoTransformer */
	private $transformer;

	public function testIdToApp()
	{
		$id = '4af9f23d8ead0e1d32000000';
		$this->assertEquals($id, $this->transformer->toApp(new \MongoId($id), 'ext_id'));
	}

	public function testIdToExt()
	{
		$id = '4af9f23d8ead0e1d32000000';
		$this->assertEquals(new \MongoId($id), $this->transformer->toExt($id, 'app_id'));
	}

	public function testDateToExtNull()
	{
		$this->assertNull($this->transformer->toExt(null, 'app_date'));

		$this->assertEquals(['ext_date' => null], $this->transformer->toExt(['app_date' => null]));
	}

	public function testDateToApp()
	{
		$date = '2014-04-20';
		$this->assertEquals(
			date_create($date),
			$this->transformer->toApp(new \MongoDate(strtotime($date)), 'ext_date')
		);
	}

	public function testDateToExt()
	{
		$date = '2014-04-20';
		$this->assertEquals(
			new \MongoDate(strtotime($date)),
			$this->transformer->toExt(date_create($date), 'app_date')
		);
	}

	public function testDateToExtEmpty()
	{
		$this->assertEquals(
			null,
			$this->transformer->toExt('', 'app_date')
		);
	}

	protected function setUp()
	{
		$this->transformer = new MongoTransformer();
	}
}

class MongoTransformer extends Transformer
{
	use MongoTrait;

	public function __construct()
	{
		$this->defineId('app_id', 'ext_id');
		$this->defineDate('app_date', 'ext_date');
	}
}
