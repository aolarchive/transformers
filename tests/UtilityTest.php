<?php

namespace Amp\Transformers\Tests;

use Amp\Transformers\Utility;

class UtilityTest extends \PHPUnit_Framework_TestCase
{
	/** @var Utility Utility class. */
	private $utility;

	public function testBitmask()
	{
		$value = $this->utility->bitmask('foo', ['foo' => 'bar']);
		$this->assertSame('bar', $value);
	}

	public function testBitmaskFlip()
	{
		$value = $this->utility->bitmask('bar', ['foo' => 'bar'], true);
		$this->assertSame('foo', $value);
	}

	public function testBoolval()
	{
		$this->assertTrue($this->utility->boolval(1));
		$this->assertFalse($this->utility->boolval(0));
	}

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
		$this->utility = new Utility();
	}
}
