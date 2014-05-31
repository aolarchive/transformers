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
}
