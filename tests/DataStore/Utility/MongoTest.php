<?php

namespace Amp\Transformers\Tests\DataStore\Utility;

use Amp\Transformers\DataStore\Utility\Mongo;

class MongoTest extends \PHPUnit_Framework_TestCase
{
	/** @var Mongo Mongo utility class. */
	private $utility;

	public function testIdToApp()
	{
		$id = '4af9f23d8ead0e1d32000000';
		$this->assertEquals($id, $this->utility->idToApp(new \MongoId($id)));
	}

	public function testIdToExt()
	{
		$id = '4af9f23d8ead0e1d32000000';
		$this->assertEquals(new \MongoId($id), $this->utility->idToExt($id));
	}

	public function testDateToExtNull()
	{
		$this->assertNull($this->utility->dateToExt(null));
	}

	public function testDateToApp()
	{
		$date = '2014-04-20';
		$this->assertEquals(
			date_create($date),
			$this->utility->dateToApp(new \MongoDate(strtotime($date)))
		);
	}

	public function testDateToExt()
	{
		$date = '2014-04-20';
		$this->assertEquals(
			new \MongoDate(strtotime($date)),
			$this->utility->dateToExt(date_create($date))
		);
	}

	public function testDateFromMongoId()
	{
		$id = new \MongoId('4af9f23d8ead0e1d32000000');

		$this->assertEquals(date_create('2009-11-10T18:07:41-0500'), $this->utility->getCreatedDateFromMongoId($id));
	}

	protected function setUp()
	{
		$this->utility = new Mongo();
	}
}
