<?php

namespace Amp\Transformers;

class Transformer
{
	const ENV_APP = 'app';
	const ENV_STORAGE = 'storage';

	/** @var Definition[] Transformation definitions */
	private $definitions = [];

	private $indexs = [];

	public function define($app_name, $storage_name, $app_func = null, $storage_func = null)
	{
		$definition = new Definition($app_name, $storage_name, $app_func, $storage_func);
		$index      = array_push($this->definitions, $definition) - 1;

		$this->indexs[self::ENV_STORAGE][$app_name] = $index;
		$this->indexs[self::ENV_APP][$storage_name] = $index;
	}

	public function forStorage($data)
	{
		return $this->forEnv(self::ENV_STORAGE, $data);
	}

	public function forApp($data)
	{
		return $this->forEnv(self::ENV_APP, $data);
	}

	private function forEnv($env, $data)
	{
		$ret = [];
		foreach ($data as $key => $value) {
			if (!isset($this->indexs[$env][$key])) {
				continue;
			}

			$index      = $this->indexs[$env][$key];
			$definition = $this->definitions[$index];

			$func_array = $definition->getFunc($env);
			$callable   = array_shift($func_array);

			if (method_exists($this, $callable)) {
				$callable = [$this, $callable];
			} elseif (is_object($callable) || class_exists($callable)) {
				$callable = [$callable, array_shift($func_array)];
			}

			if (is_callable($callable)) {
				array_unshift($func_array, $value);
				$value = call_user_func_array($callable, $func_array);
			}

			$ret[$definition->getKey($env)] = $value;
		}

		return $ret;
	}


	/* Utility transformation methods ****************************************/

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
