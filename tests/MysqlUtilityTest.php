<?php

namespace Amp\Transformers\Tests;

use Amp\Transformers\MysqlUtility;

class MysqlUtilityTest extends \PHPUnit_Framework_TestCase
{
	/** @var MysqlUtility Utility class. */
	private $utility;

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testDateToMySqlException()
	{
		$this->utility->convertDateToMySql(new \StdClass());
	}

	/**
	 * @dataProvider dateProvider
	 */
	public function testDateToMySql($expected, $date, $time)
	{
		$this->assertSame($expected, $this->utility->convertDateToMySql($date, $time));
	}

	public function dateProvider()
	{
		return [
			['2014-01-01 00:00:00', date_create('2014-01'), true],
			['2014-01-01', '2014-01', false],
			['2014-01-01', date_create('2014-01'), false],
			[null, '', null]
		];
	}

	protected function setUp()
	{
		$this->utility = new MysqlUtility();
	}
}
