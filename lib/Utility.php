<?php

namespace Aol\Transformers;

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
	 * Convert all dates in an array to a standardized format.
	 * Recurses into nested arrays.
	 *
	 * @param array  $array  The array to check.
	 * @param string $format Date format. Defaults to ISO-8601.
	 * @return array The corrected array.
	 */
	public function formatDatesInArray(array $array, $format = \DateTime::ISO8601)
	{
		foreach ($array as &$value) {
			if ($value instanceof \DateTime) {
				try {
					$value = $value->format($format);
				} catch (\Exception  $e) {

				}
			} elseif (is_array($value)) {
				$value = $this->formatDatesInArray($value);
			}
		}

		return $array;
	}
}
