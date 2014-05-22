<?php

namespace Amp\Transformers\Tests;

use Amp\Transformers\Transformer;
use Amp\Transformers\Utility;

class TransformerTest extends \PHPUnit_Framework_TestCase
{
	/** @var Transformer */
	private $transformer;

	public function testTransformNameToApp()
	{
		$this->transformer->define('id', 'postid');
		$data = $this->transformer->forApp(['postid' => 5]);

		$this->assertSame(['id' => 5], $data);
	}

	public function testTransformNameToStorage()
	{
		$this->transformer->define('id', 'postid');
		$data = $this->transformer->forStorage(['id' => 5]);

		$this->assertSame(['postid' => 5], $data);
	}

	public function testTransformValueToApp()
	{
		$this->transformer->define('id', 'postid', 'intval');
		$data = $this->transformer->forApp(['postid' => '5']);

		$this->assertSame(['id' => 5], $data);
	}

	public function testTransformValueToStorage()
	{
		$this->transformer->define('id', 'postid', null, 'strval');
		$data = $this->transformer->forStorage(['id' => 5]);

		$this->assertSame(['postid' => '5'], $data);
	}


	/* Utility transformation methods ****************************************/

	public function testBitmask()
	{
		$this->transformer->defineMask('status', 'status', [5 => 'foo', 6 => 'bar']);

		$data = $this->transformer->forApp(['status' => 5]);
		$this->assertSame(['status' => 'foo'], $data);

		$data = $this->transformer->forStorage(['status' => 'bar']);
		$this->assertSame(['status' => 6], $data);
	}

	public function testConvertDateTimeToMysql()
	{
		$data = ['date' => date_create('2014-01-01')];
		$this->transformer->defineDate('date', 'date');

		$data = $this->transformer->forStorage($data);
		$this->assertSame(['date' => '2014-01-01 00:00:00'], $data);
	}

	public function testJson()
	{
		$this->transformer->defineJson('metadata', 'metadata');

		$array = ['metadata' => ['foo' => 'bar']];
		$json = ['metadata' => '{"foo":"bar"}'];

		$data = $this->transformer->forStorage($array);
		$this->assertSame($json, $data);

		$data = $this->transformer->forApp($json);
		$this->assertSame($array, $data);
	}

	protected function setUp()
	{
		$this->transformer = new Transformer(new Utility());
	}
}
