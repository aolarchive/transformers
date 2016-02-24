<?php

namespace Aol\Transformers\Tests\Utilities;

use Aol\Transformers\Transformer;
use Aol\Transformers\Utilities\MongoDBTrait;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;

class MongoDBTest extends \PHPUnit_Framework_TestCase
{
	/** @var MongoDBTransformer */
	private $transformer;

	public function testIdToApp()
	{
		$id = '4af9f23d8ead0e1d32000000';
		$this->assertEquals($id, $this->transformer->toApp(new ObjectID($id), 'ext_id'));
	}

	public function testIdToExt()
	{
		$id = '4af9f23d8ead0e1d32000000';
		$this->assertEquals(new ObjectID($id), $this->transformer->toExt($id, 'app_id'));
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
			$this->transformer->toApp(new UTCDateTime(strtotime($date)*1000), 'ext_date')
		);
	}

	public function testDateToExt()
	{
		$date = '2014-04-20';
		$this->assertEquals(
			new UTCDateTime(strtotime($date)*1000),
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

	public function testEscapeMongoKeys()
	{
		$data = [
			'$dollar'    => '$dollar$sign',
			'dot.symbol' => 'these.are.dots',
			'is$dollar'  => true,
			'foo'        => 'bar'
		];
		$escaped = [
			'＄dollar'    => '$dollar$sign',
			'dot．symbol' => 'these.are.dots',
			'is＄dollar'  => true,
			'foo'        => 'bar'
		];

		$this->assertEquals(
			$escaped,
			$this->transformer->escapeMongoKeys($data)
		);
	}

	public function testUnescapeMongoKeys()
	{
		$data = [
			'＄dollar'    => '$dollar$sign',
			'dot．symbol' => 'these.are.dots',
			'is＄dollar'  => true,
			'foo'        => 'bar'
		];
		$unescaped = [
			'$dollar'    => '$dollar$sign',
			'dot.symbol' => 'these.are.dots',
			'is$dollar'  => true,
			'foo'        => 'bar'
		];

		$this->assertEquals(
			$unescaped,
			$this->transformer->unescapeMongoKeys($data)
		);
	}

	protected function setUp()
	{
		$this->transformer = new MongoDBTransformer();
	}
}

class MongoDBTransformer extends Transformer
{
	use MongoDBTrait;

	public function __construct()
	{
		$this->defineId('app_id', 'ext_id');
		$this->defineDate('app_date', 'ext_date');
	}
}
