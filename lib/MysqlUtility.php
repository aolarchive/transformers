<?php

namespace Amp\Transformers;

class MysqlUtility extends Utility
{
	/**
	 * Converts a date to MySQL format.
	 *
	 * @param string|\DateTime $date The date to convert.
	 * @param bool             $time
	 * @return string|null The converted MySQL formatted date string or null.
	 */
	public function convertDateToMySql($date, $time = true)
	{
		if (empty($date)) {
			return null;
		}

		if (is_string($date)) {
			$date = new \DateTime($date);
		}

		if (!$date instanceof \DateTime) {
			throw new \InvalidArgumentException;
		}

		$format = $time
			? 'Y-m-d H:i:s'
			: 'Y-m-d';

		return $date->format($format);
	}
}
