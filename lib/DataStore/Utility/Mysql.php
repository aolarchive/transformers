<?php

namespace Aol\Transformers\DataStore\Utility;

use Aol\Transformers\DataStore\UtilityInterface;
use Aol\Transformers\Utility;

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
		// If the value is empty, or a string containing
		// *only* characters (' ', '-', '_', ':', '\', '/', '0'), then the DateTime is considered null.
		if (empty($date) || (is_string($date) && preg_match('~^[ \\-_:\\\\/0]+$~', $date))) {
			return null;
		} else {
			return new \DateTime($date);
		}
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
		return $this->dateToApp($date);
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
