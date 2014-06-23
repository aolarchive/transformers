<?php

namespace Aol\Transformers\Tests;

use Aol\Transformers\Utility;

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
	 * @dataProvider iso8601Provider
	 */
	public function testConvertDateToIso8601($input, $format, $expected)
	{
		$actual = $this->utility->formatDatesInArray([$input], $format);
		$this->assertEquals([$expected], $actual);
	}

	public function iso8601Provider()
	{
		return [
			[new \DateTime('2014-01-01T00:00:00-0500'), \DateTime::ISO8601, '2014-01-01T00:00:00-0500'],
			[new \DateTime('2014-01-01T00:00:00-0500'), 'Y-m-d', '2014-01-01']
		];
	}

	protected function setUp()
	{
		$this->utility = new Utility();
	}
}
