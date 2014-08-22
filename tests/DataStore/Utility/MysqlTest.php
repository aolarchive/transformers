<?php

namespace Aol\Transformers\Tests\DataStore\Utility;

use Aol\Transformers\DataStore\Utility\Mysql;

class MysqlTest extends \PHPUnit_Framework_TestCase
{
	/** @var Mysql Mysql utility class. */
	private $utility;

	public function testIdToApp()
	{
		$this->assertSame(5, $this->utility->idToApp('5'));
	}

	public function testIdToExt()
	{
		$this->assertSame('5', $this->utility->idToExt(5));
	}

	public function testDateToApp()
	{
		$date = '2014-04-20';
		$this->assertEquals(date_create($date), $this->utility->dateToApp($date));
	}

	public function testZeroedDateToApp()
	{
		$date = '0000-00-00 00:00:00';
		$this->assertEquals(null, $this->utility->dateToApp($date));
	}

	public function testDateToExt()
	{
		$date = '2014-04-20';
		$this->assertEquals($date, $this->utility->dateToExt(date_create($date)));
	}

	public function testDateTimeToApp()
	{
		$date = '2014-04-20 23:10:15';
		$this->assertEquals(date_create($date), $this->utility->dateTimeToApp($date));
	}

	public function testDateTimeToExt()
	{
		$date = '2014-04-20 23:10:15';
		$this->assertEquals($date, $this->utility->dateTimeToExt(date_create($date)));
	}

	protected function setUp()
	{
		$this->utility = new Mysql();
	}
}
