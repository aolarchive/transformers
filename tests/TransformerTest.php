<?php

namespace Amp\Transformers\Tests;

use Amp\Transformers\Transformer;
use Amp\Transformers\Utility;

class TransformerTest extends \PHPUnit_Framework_TestCase
{
	/** @var Transformer Transformer object. */
	private $transformer;

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testInvalidEnvironmentShouldThrowException()
	{
		$this->transformer->to('badenv', ['foo' => 'bar']);
	}

	public function testNonArrayEmptyValueShouldReturnNull()
	{
		$actual = $this->transformer->toApp('');

		$this->assertNull($actual);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testInvalidKeyShouldThrowException()
	{
		$this->transformer->toApp([], 'badkey');
	}

	/**
	 * @dataProvider dataProvider
	 */
	public function testData($app, $ext, $check_type = true)
	{
		if ($check_type) {
			$this->assertSame($app, $this->transformer->toApp($ext));
			$this->assertSame($ext, $this->transformer->toExt($app));
		} else {
			$this->assertEquals($app, $this->transformer->toApp($ext));
			$this->assertEquals($ext, $this->transformer->toExt($app));
		}
	}

	/**
	 * @dataProvider dataProvider
	 */
	public function testDataKey($app, $ext, $check_type = true)
	{
		$app_key = key($app);
		$app_val = $app[$app_key];
		$ext_key = key($ext);
		$ext_val = $ext[$ext_key];

		if ($check_type) {
			$this->assertSame($app_val, $this->transformer->toApp($ext_val, $ext_key));
			$this->assertSame($ext_val, $this->transformer->toExt($app_val, $app_key));
		} else {
			$this->assertEquals($app_val, $this->transformer->toApp($ext_val, $ext_key));
			$this->assertEquals($ext_val, $this->transformer->toExt($app_val, $app_key));
		}
	}

	public function testGetKeysApp()
	{
		$actual   = $this->transformer->getKeysApp();
		$expected = ['id', 'title', 'body', 'meta', 'status'];
		$this->assertEquals($expected, $actual);
	}

	public function testGetKeysExt()
	{
		$actual   = $this->transformer->getKeysExt();
		$expected = ['postid', 'title', 'Content', 'MetaData', 'status'];
		$this->assertEquals($expected, $actual);
	}

	public function testDataArray()
	{
		$app = ['published', 'draft'];
		$ext = [2, 1];

		$this->assertSame($app, $this->transformer->toApp($ext, 'status', true));
		$this->assertSame($ext, $this->transformer->toExt($app, 'status', true));
	}

	public function testFqcn()
	{
		$this->assertSame('Amp\\Transformers\\Transformer', $this->transformer->fqcn());
	}

	public function dataProvider()
	{
		return [
			[['title' => 'foobar'], ['title' => 'foobar']], // Pass thru
			[['body' => 'foobar'], ['Content' => 'foobar']], // Simple mapping
			[['id' => 5], ['postid' => '5']], // Mapping with callable

			// Test definition shortcuts
			[['meta' => ['foo' => 'bar']], ['MetaData' => '{"foo":"bar"}']], // Json
			[['status' => 'draft'], ['status' => 1]], // Mask
		];
	}

	protected function setUp()
	{
		$transformer = new Transformer(new Utility());
		$transformer->define('id', 'postid', 'intval', 'strval');
		$transformer->define('title', 'title');
		$transformer->define('body', 'Content');
		$transformer->defineJson('meta', 'MetaData');
		$transformer->defineMask('status', 'status', [1 => 'draft', 2 => 'published']);

		$this->transformer = $transformer;
	}
}
