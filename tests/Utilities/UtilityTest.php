<?php

namespace Aol\Transformers\Tests\Utilities;

use Aol\Transformers\Transformer;
use Aol\Transformers\Utilities\UtilityTrait;

class UtilityTest extends \PHPUnit_Framework_TestCase
{
	/** @var UtilityTransformer */
	private $transformer;

	public function testJsonToApp()
	{
		$this->assertSame(
			['foo' => 'bar'],
			$this->transformer->toApp('{"foo":"bar"}', 'ext_json')
		);
	}

	public function testJsonToExt()
	{
		$this->assertSame(
			'{"foo":"bar"}',
			$this->transformer->toExt(['foo' => 'bar'], 'app_json')
		);
	}

	public function testMaskToApp()
	{
		$this->assertSame(
			'bar',
			$this->transformer->toApp(2, 'ext_mask')
		);
	}

	public function testMaskToExt()
	{
		$this->assertSame(
			2,
			$this->transformer->toExt('bar', 'app_mask')
		);
	}

	protected function setUp()
	{
		$this->transformer = new UtilityTransformer;
	}
}

class UtilityTransformer extends Transformer
{
	use UtilityTrait;

	protected function definitions()
	{
		$this->defineJson('app_json', 'ext_json');
		$this->defineMask('app_mask', 'ext_mask', [1 => 'foo', 2 => 'bar']);
	}
}
