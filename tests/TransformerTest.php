<?php

namespace Amp\Transformers\Tests;

use Amp\Transformers\Transformer;

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
		$mask = [5 => 'foo', 6 => 'bar'];
		$this->transformer->define('status', 'status', ['bitmask', $mask], ['bitmask', $mask, 'true']);

		$data = $this->transformer->forApp(['status' => 5]);
		$this->assertSame(['status' => 'foo'], $data);

		$data = $this->transformer->forStorage(['status' => 'bar']);
		$this->assertSame(['status' => 6], $data);
	}

	public function testBoolVal()
	{
		$this->transformer->define('status', 'status', 'boolval');
		$data = $this->transformer->forApp(['status' => 1]);

		$this->assertSame(['status' => true], $data);

		$this->transformer->define('status', 'status', 'boolval');
		$data = $this->transformer->forApp(['status' => 0]);

		$this->assertSame(['status' => false], $data);
	}

	public function testConvertDateTimeToMysql()
	{
		$data = ['date' => date_create('2014-01-01')];
		$this->transformer->define('date', 'date', null, 'convertDateToMySql');

		$data = $this->transformer->forStorage($data);
		$this->assertSame(['date' =>'2014-01-01 00:00:00'], $data);
	}

    protected function setUp()
    {
        $this->transformer = new Transformer();
    }
}
