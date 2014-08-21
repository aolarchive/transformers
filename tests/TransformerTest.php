<?php

namespace Aol\Transformers\Tests;

use Aol\Transformers\Transformer;
use Aol\Transformers\Utility;

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

		$actual   = $this->transformer->getKeysApp('test.');
		$expected = ['test.id', 'test.title', 'test.body', 'test.meta', 'test.status',];
		$this->assertEquals($expected, $actual);
	}

	public function testGetKeysExt()
	{
		$actual   = $this->transformer->getKeysExt();
		$expected = ['postid', 'title', 'Content', 'MetaData', 'status'];
		$this->assertEquals($expected, $actual);

		$actual   = $this->transformer->getKeysExt('test.');
		$expected = ['test.postid', 'test.title', 'test.Content', 'test.MetaData', 'test.status',];
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
		$this->assertSame('Aol\\Transformers\\Transformer', $this->transformer->fqcn());
	}

	public function testBoolValues()
	{
		$utility     = new Utility();
		$transformer = new Transformer($utility);
		$transformer->define('public', 'public', [$utility, 'boolval'], 'intval');

		$this->assertSame(['public' => 0], $transformer->toExt(['public' => false]));
		$this->assertSame(['public' => 1], $transformer->toExt(['public' => true]));
		$this->assertsame(0, $transformer->toExt(false, 'public'));
		$this->assertsame(1, $transformer->toExt(true, 'public'));
	}

	public function testVirtualDataIsPassedThru()
	{
		$actual = $this->transformer->toApp(['postid' => 32, 'virtual_field' => 'foo']);

		$this->assertEquals(['id' => 32, 'virtual_field' => 'foo'], $actual);
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
		$transformer->defineVirtual('virtual_field');

		$this->transformer = $transformer;
	}
}
