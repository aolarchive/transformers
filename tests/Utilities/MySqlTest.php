<?php

namespace Aol\Transformers\Tests\DataStore\Utility;

use Aol\Transformers\Transformer;
use Aol\Transformers\Utilities\MySqlTrait;

class MySqlTest extends \PHPUnit_Framework_TestCase
{
	/** @var MysqlTransformer */
	private $transformer;

	public function testIdToApp()
	{
		$this->assertSame(5, $this->transformer->toApp('5', 'ext_id'));
	}

	public function testIdToExt()
	{
		$this->assertSame('5', $this->transformer->toExt(5, 'app_id'));
	}

	public function testDateToApp()
	{
		$date = '2014-04-20';
		$this->assertEquals(date_create($date), $this->transformer->toApp($date, 'ext_date'));
	}

	public function testZeroedDateToApp()
	{
		$date = '0000-00-00 00:00:00';
		$this->assertEquals(null, $this->transformer->toApp($date, 'ext_date'));
	}

	public function testZeroedDateTimeToApp()
	{
		$date = '0000-00-00 00:00:00';
		$this->assertEquals(null, $this->transformer->toApp($date, 'ext_datetime'));
	}

	public function testDateToExt()
	{
		$date = '2014-04-20';
		$this->assertEquals($date, $this->transformer->toExt(date_create($date), 'app_date'));
	}

	public function testDateTimeToApp()
	{
		$date = '2014-04-20 23:10:15';
		$this->assertEquals(date_create($date), $this->transformer->toApp($date, 'ext_datetime'));
	}

	public function testDateTimeToExt()
	{
		$date = '2014-04-20 23:10:15';
		$this->assertEquals($date, $this->transformer->toExt(date_create($date), 'app_datetime'));
	}

	protected function setUp()
	{
		$this->transformer = new MysqlTransformer();
	}
}

class MysqlTransformer extends Transformer
{
	use MySqlTrait;

	public function __construct()
	{
		$this->defineId('app_id', 'ext_id');
		$this->defineDate('app_date', 'ext_date');
		$this->defineDateTime('app_datetime', 'ext_datetime');
	}
}
