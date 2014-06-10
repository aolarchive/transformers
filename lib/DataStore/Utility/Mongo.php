<?php

namespace Aol\Transformers\DataStore\Utility;

use Aol\Transformers\DataStore\UtilityInterface;
use Aol\Transformers\Utility;
use MongoDate;
use MongoId;

class Mongo extends Utility implements UtilityInterface
{
	/**
	 * @param \MongoId|null $id
	 * @return string
	 */
	public function idToApp($id)
	{
		return (string)$id;
	}

	/**
	 * @param string $id
	 * @return MongoId|null
	 */
	public function idToExt($id)
	{
		return empty($id) ? null : new MongoId($id);
	}

	/**
	 * @param \MongoDate|null $date
	 * @return \DateTime|null
	 */
	public function dateTimeToApp($date)
	{
		return $date instanceof MongoDate ? new \DateTime('@' . $date->sec) : null;
	}

	/**
	 * @param \DateTime $date
	 * @return MongoDate|null
	 */
	public function dateTimeToExt($date)
	{
		if (empty($date)) {
			return null;
		}

		$date = $date instanceof \DateTime ? $date->getTimestamp() : strtotime($date);

		return new MongoDate($date);
	}

	/**
	 * @param mixed $date
	 * @return \DateTime|null
	 */
	public function dateToApp($date)
	{
		return $this->dateTimeToApp($date);
	}

	/**
	 * @param \DateTime|null $date
	 * @return MongoDate|null
	 */
	public function dateToExt($date)
	{
		return $this->dateTimeToExt($date);
	}

	// Additional methods
	///////////////////////////////////////////////////////////////////////////

	/**
	 * Get created DateTime from a MongoId object.
	 *
	 * @param MongoId $id
	 * @return \DateTime
	 */
	public function getDateFromMongoId(MongoId $id)
	{
		$date_time = new \DateTime('@' . $id->getTimestamp());
		$date_time->setTimezone(new \DateTimeZone(date_default_timezone_get()));

		return $date_time;
	}

	/**
	 * Build a new MongoId instance from a given date.
	 *
	 * @see http://jwage.com/post/55617183676/mongodb-php-mongodate-tricks
	 *
	 * @param mixed $date Date to use to generate MongoId; can be unix timestamp, DateTime, or date string
	 * @return MongoId
	 */
	public function getMongoIdFromDate($date)
	{
		static $inc = 0;

		if ($date instanceof \DateTime) {
			$timestamp = $date->getTimestamp();
		} else if (is_int($date)) {
			$timestamp = $date;
		} else {
			$timestamp = strtotime($date);
		}

		if (empty($timestamp)) {
			return null;
		}

		$ts = pack('N', $timestamp);
		$m = substr(md5(gethostname()), 0, 3);
		$pid = pack('n', posix_getpid());
		$trail = substr(pack('N', $inc++), 1, 3);

		$bin = sprintf('%s%s%s%s', $ts, $m, $pid, $trail);

		$id = '';
		for ($i = 0; $i < 12; $i++ ) {
			$id .= sprintf('%02x', ord($bin[$i]));
		}

		return new \MongoId($id);
	}
}
