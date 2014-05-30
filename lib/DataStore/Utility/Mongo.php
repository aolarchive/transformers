<?php

namespace Amp\Transformers\DataStore\Utility;

use Amp\Transformers\DataStore\UtilityInterface;
use Amp\Transformers\Utility;
use MongoDate;
use MongoId;

class Mongo extends Utility implements UtilityInterface
{
	/**
	 * @param \MongoId|null $id
	 * @return int
	 */
	public function idToApp($id)
	{
		return (string)$id;
	}

	/**
	 * @param int $id
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

	public function getCreatedDateFromMongoId(MongoId $id)
	{
		$date_time = new \DateTime('@' . $id->getTimestamp());
		$date_time->setTimezone(new \DateTimeZone(date_default_timezone_get()));

		return $date_time;
	}
}
