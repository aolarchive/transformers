<?php

namespace Amp\Transformers;

use MongoDate;
use MongoId;

class MongoUtility extends Utility
{
	public function convertStringToMongoId($str)
	{
		if (empty($str)) {
			return null;
		}

		return new MongoId($str);
	}

	public function convertMongoIdToString(MongoId $id)
	{
		return (string)$id;
	}

	public function getCreatedDateFromMongoId(MongoId $id)
	{
		if (empty($id)) {
			return null;
		}

		$date_time = new \DateTime('@' . $id->getTimestamp());
		$date_time->setTimezone(new \DateTimeZone(date_default_timezone_get()));

		return $date_time;
	}

	public function convertMongoDateToDateTime(MongoDate $mdate)
	{
		if (empty($mdate)) {
			return null;
		}

		$date_time = new \DateTime('@' . $mdate->sec);
		$date_time->setTimezone(new \DateTimeZone(date_default_timezone_get()));

		return $date_time;
	}

	public function convertDateToMongoDate($date)
	{
		if (empty($date)) {
			return null;
		}

		if ($date instanceof \DateTime) {
			$mdate = new MongoDate($date->getTimestamp());
		} else {
			$mdate = new MongoDate(strtotime($date));
		}

		return $mdate;
	}
}
