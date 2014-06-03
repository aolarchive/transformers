<?php

namespace Amp\Transformers\DataStore\Utility;

use Amp\Transformers\DataStore\UtilityInterface;
use Amp\Transformers\Utility;

class Mysql extends Utility implements UtilityInterface
{
	/**
	 * @param mixed $id
	 * @return int
	 */
	public function idToApp($id)
	{
		return (int)$id;
	}

	/**
	 * @param int $id
	 * @return string
	 */
	public function idToExt($id)
	{
		return (string)$id;
	}

	/**
	 * @param mixed $date
	 * @return \DateTime|null
	 */
	public function dateToApp($date)
	{
		return empty($date) ? null : new \DateTime($date);
	}

	/**
	 * @param \DateTime|null $date
	 * @return null|string
	 */
	public function dateToExt($date)
	{
		return !$date instanceof \DateTime ? null : $date->format('Y-m-d');
	}

	/**
	 * @param mixed $date
	 * @return \DateTime|null
	 */
	public function dateTimeToApp($date)
	{
		return empty($date) ? null : new \DateTime($date);
	}

	/**
	 * @param \DateTime $date
	 * @return null|string
	 */
	public function dateTimeToExt($date)
	{
		return !$date instanceof \DateTime ? null : $date->format('Y-m-d H:i:s');
	}
}
