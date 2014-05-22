<?php

namespace Amp\Transformers;

class Utility
{
	/**
	 * Maps string values to int values.
	 *
	 * @param string $value
	 * @param array  $mask
	 * @param bool   $flip
	 * @return string|null
	 */
	public function bitmask($value, $mask, $flip = false)
	{
		if ($flip) {
			$mask = array_flip($mask);
		}

		return isset($mask[$value]) ? $mask[$value] : null;
	}

	/**
	 * Converts a mixed value to a boolean value.
	 *
	 * @param mixed $value
	 * @return bool
	 */
	public function boolval($value)
	{
		return !!$value;
	}

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
