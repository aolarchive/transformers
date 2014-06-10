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

	protected function setUp()
	{
		$this->utility = new Utility();
	}
}
