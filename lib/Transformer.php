<?php

namespace Amp\Transformers;

class Transformer
{
	const ENV_APP = 'app';
	const ENV_STORAGE = 'storage';

	/** @var array Transformation definitions */
	private $definitions = [];

	/**
	 * Saves field definitions.
	 *
	 * @param string $app_name     Property name in application context.
	 * @param string $storage_name Property name storage context.
	 * @param null   $app_func     Callable for transforming property to app context
	 * @param null   $storage_func Callable for transforming property to storage context.
	 */
	public function define($app_name, $storage_name, $app_func = null, $storage_func = null)
	{
		$this->definitions[self::ENV_STORAGE][$app_name] = [
			'key'      => $storage_name,
			'callback' => $this->saveCallback($storage_func)
		];
		$this->definitions[self::ENV_APP][$storage_name] = [
			'key'      => $app_name,
			'callback' => $this->saveCallback($app_func)
		];
	}

	/**
	 * Defines a date property.
	 *
	 * App format: DateTime object
	 * Storage format: YYYY-MM-DD HH:MM:SS
	 *
	 * @param string $app_name     Property name in application context.
	 * @param string $storage_name Property name storage context.
	 */
	public function defineDate($app_name, $storage_name)
	{
		$this->define($app_name, $storage_name, 'date_create', 'convertDateToMysql');
	}

	/**
	 * Defines a property that is stored as JSON. Expands to an array in app.
	 *
	 * @param string $app_name     Property name in application context.
	 * @param string $storage_name Property name storage context.
	 */
	public function defineJson($app_name, $storage_name)
	{
		$this->define($app_name, $storage_name, 'json_decode', 'json_encode');
	}

	/**
	 * @param string $app_name     Property name in application context.
	 * @param string $storage_name Property name storage context.
	 * @param array  $mask
	 */
	public function defineMask($app_name, $storage_name, $mask)
	{
		$this->define($app_name, $storage_name, ['bitmask', $mask], ['bitmask', $mask, true]);
	}

	/**
	 * Transforms data for storage.
	 *
	 * @param array $data Data for transformation.
	 * @return array
	 */
	public function forStorage($data)
	{
		return $this->forEnv(self::ENV_STORAGE, $data);
	}

	/**
	 * Transforms data for app.
	 *
	 * @param array $data Data for transformation.
	 * @return array
	 */
	public function forApp($data)
	{
		return $this->forEnv(self::ENV_APP, $data);
	}

	/**
	 * Transforms data for a specific environment.
	 *
	 * @param string $env  Environment name.
	 * @param array  $data Data for transformation.
	 * @return array
	 */
	public function forEnv($env, $data)
	{
		$ret = [];
		foreach ($data as $key => $value) {
			if ($definition = $this->getDefinition($env, $key)) {
				$ret[$definition['key']] = $this->runCallback($definition['callback'], $value);
			}
		}

		return $ret;
	}

	/**
	 * Gets a property definition by key for a specific environment.
	 *
	 * @param string $env Environment name.
	 * @param string $key Key name.
	 * @return null
	 */
	private function getDefinition($env, $key)
	{
		return isset($this->definitions[$env][$key])
			? $this->definitions[$env][$key]
			: null;
	}

	/**
	 * Saves a transformation data callback.
	 *
	 * @param array $func_array
	 * @return array|null
	 */
	private function saveCallback($func_array)
	{
		if ($func_array !== null) {
			if (is_string($func_array)) {
				$func_array = [$func_array];
			}

			$callable = array_shift($func_array);

			// Class methods take first priority
			if (method_exists($this, $callable)) {
				$callable = [$this, $callable];
			} // Next parse out a real callable
			elseif (is_object($callable) || class_exists($callable)) {
				$callable = [$callable, array_shift($func_array)];
			}

			// If we don't have a callable now there's a problem.
			if (!is_callable($callable)) {
				throw new \InvalidArgumentException;
			}

			$func_array = ['callable' => $callable, 'args' => $func_array];
		}

		return $func_array;
	}

	/**
	 * Processes transformation using a saved callback.
	 *
	 * @param array $func_array 
	 * @param mixed $value
	 * @return array|null
	 */
	private function runCallback($func_array, $value)
	{
		if ($func_array !== null) {
			array_unshift($func_array['args'], $value);
			$value = call_user_func_array($func_array['callable'], $func_array['args']);
		}

		return $value;
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
