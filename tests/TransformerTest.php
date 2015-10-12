<?php

namespace Aol\Transformers\Tests;

use Aol\Transformers\Transformer;

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

	public function testDataArray()
	{
		$this->assertSame(
			[21, 12],
			$this->transformer->toApp(['21', '12'], 'postid', true)
		);
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
		$expected = ['id', 'title', 'body', 'meta'];
		$this->assertEquals($expected, $actual);

		$actual   = $this->transformer->getKeysApp('test.');
		$expected = ['test.id', 'test.title', 'test.body', 'test.meta'];
		$this->assertEquals($expected, $actual);
	}

	public function testGetKeysExt()
	{
		$actual   = $this->transformer->getKeysExt();
		$expected = ['postid', 'title', 'Content', 'MetaData'];
		$this->assertEquals($expected, $actual);

		$actual   = $this->transformer->getKeysExt('test.');
		$expected = ['test.postid', 'test.title', 'test.Content', 'test.MetaData'];
		$this->assertEquals($expected, $actual);
	}

	public function testVirtualDataIsPassedThru()
	{
		$actual = $this->transformer->toApp(['postid' => 32, 'virtual_field' => 'foo']);

		$this->assertEquals(['id' => 32, 'virtual_field' => 'foo'], $actual);
	}

	public function testGetKey()
	{
		$this->assertEquals('postid', $this->transformer->getKeyApp('id'));
		$this->assertEquals('body', $this->transformer->getKeyExt('Content'));
	}

	public function testGetMap()
	{
		$expected = [
				'id'    => 'postid',
				'title' => 'title',
				'body'  => 'Content',
				'meta'  => 'MetaData',
		];

		$this->assertEquals($expected, $this->transformer->getMap());
		$this->assertEquals(array_flip($expected), $this->transformer->getMap(Transformer::EXT));
	}

	public function dataProvider()
	{
		return [
			[['title' => 'foobar'], ['title' => 'foobar']], // Pass thru
			[['body' => 'foobar'], ['Content' => 'foobar']], // Simple mapping
			[['id' => 5], ['postid' => '5']], // Mapping with callable

			// Test definition shortcuts
			[['meta' => ['foo' => 'bar']], ['MetaData' => '{"foo":"bar"}']], // Json
		];
	}

	protected function setUp()
	{
		$transformer = new Transformer();
		$transformer->define('id', 'postid', 'intval', 'strval');
		$transformer->define('title', 'title');
		$transformer->define('body', 'Content');
		$transformer->define('meta', 'MetaData', 'json_decode', 'json_encode', [true]);
		$transformer->defineVirtual('virtual_field');

		$this->transformer = $transformer;
	}
}
