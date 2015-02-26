<?php

namespace Aol\Transformers;

use Aol\Transformers\Exceptions\InvalidArgumentException;

class Transformer
{
	const APP = 'app';
	const EXT = 'ext';

	const DEFINITION_KEY = 'key';
	const DEFINITION_FUNC = 'func';
	const DEFINITION_ARGS = 'args';

	/** @var array Transformation definitions */
	private $definitions = [];

	/** @var array Virtual fields */
	private $virtual_fields = [];

	public function __construct()
	{
		$this->definitions();
	}

	/**
	 * Saves field definitions.
	 *
	 * @param string   $app_name Property name in application context.
	 * @param string   $ext_name Property name storage context.
	 * @param callable $app_func Callable for transforming property to app context.
	 * @param callable $ext_func Callable for transforming property to storage context.
	 * @param array    $app_args Arguments for app callback.
	 * @param array    $ext_args Arguments for storage callback.
	 */
	public function define(
		$app_name,
		$ext_name,
		callable $app_func = null,
		callable $ext_func = null,
		$app_args = [],
		$ext_args = []
	) {
		$this->definitions[self::EXT][$app_name] = [
			self::DEFINITION_KEY  => $ext_name,
			self::DEFINITION_FUNC => $ext_func,
			self::DEFINITION_ARGS => $ext_args
		];
		$this->definitions[self::APP][$ext_name] = [
			self::DEFINITION_KEY  => $app_name,
			self::DEFINITION_FUNC => $app_func,
			self::DEFINITION_ARGS => $app_args,
		];
	}



	/**
	 * Defines a virtual field that simply acts as a passthru and does not
	 * appear in getKeys().
	 *
	 * @param string $key Virtual field key name
	 */
	public function defineVirtual($key)
	{
		$this->virtual_fields[$key] = true;
	}

	/**
	 * Transforms data for app.
	 *
	 * @param mixed $data Data for transformation.
	 * @param null  $key
	 * @param bool  $array
	 * @return array
	 */
	public function toApp($data, $key = null, $array = false)
	{
		return $this->to(self::APP, $data, $key, $array);
	}

	/**
	 * Transforms data for storage.
	 *
	 * @param mixed $data Data for transformation.
	 * @param null  $key
	 * @param bool  $array
	 * @return array
	 */
	public function toExt($data, $key = null, $array = false)
	{
		return $this->to(self::EXT, $data, $key, $array);
	}

	/**
	 * Transforms data for a specific environment.
	 *
	 * @param string      $env  Environment name.
	 * @param mixed       $data Data for transformation.
	 * @param null|string $key
	 * @param bool        $array
	 * @return array
	 */
	public function to($env, $data, $key = null, $array = false)
	{
		$this->validateEnvironment($env);

		if (!is_array($data) && empty($data) && $data !== false) {
			return null;
		}

		// Handle arrays by recursion
		if ($array) {
			$map = function ($data) use ($env, $key) {
				return $this->to($env, $data, $key);
			};

			return array_map($map, $data);
		}

		// Handle keys
		if (!is_null($key)) {
			if (!$def = $this->getDefinition($env, $key)) {
				throw new InvalidArgumentException('Unknown key: ' . $key);
			}

			return $this->parseDefinitionValue($def, $data);
		}

		// Transform a data set
		$method_before = 'before' . ucfirst($env);
		$method_after  = 'after' . ucfirst($env);
		$ret           = [];

		$data = $this->$method_before($data);
		foreach ($data as $key => $value) {
			if ($def = $this->getDefinition($env, $key)) {
				$ret[$def[self::DEFINITION_KEY]] = $this->parseDefinitionValue($def, $value);
			} elseif (array_key_exists($key, $this->virtual_fields)) {
				$ret[$key] = $value;
			}
		}
		$ret = $this->$method_after($ret);

		return $ret;
	}

	public function getKeysApp($prefix = null)
	{
		return $this->getKeys(self::APP, $prefix);
	}

	public function getKeysExt($prefix = null)
	{
		return $this->getKeys(self::EXT, $prefix);
	}

	public function getKeys($env, $prefix = null)
	{
		$this->validateEnvironment($env);

		$env  = $env === self::APP ? self::EXT : self::APP;
		$keys = array_keys($this->definitions[$env]);

		if ($prefix !== null) {
			$keys = array_map(function($key) use($prefix) { return $prefix . $key; }, $keys);
		}

		return $keys;
	}

	/**
	 * No-op. To be used in subclass to setup definitions.
	 */
	protected function definitions()
	{

	}

	protected function beforeApp($data)
	{
		return $data;
	}

	protected function beforeExt($data)
	{
		return $data;
	}

	protected function afterApp($data)
	{
		return $data;
	}

	protected function afterExt($data)
	{
		return $data;
	}

	/**
	 * Gets a property definition by key for a specific environment.
	 *
	 * @param string $env Environment name.
	 * @param string $key Key name.
	 * @return array|null
	 */
	private function getDefinition($env, $key)
	{
		return isset($this->definitions[$env][$key])
			? $this->definitions[$env][$key]
			: null;
	}

	private function validateEnvironment($env)
	{
		if (!in_array($env, [self::APP, self::EXT])) {
			throw new InvalidArgumentException('Unknown environment: ' . $env);
		}
	}

	/**
	 * Parses a value based on a definition.
	 *
	 * @param array $definition Definition array.
	 * @param mixed $value      Value.
	 * @return mixed
	 */
	private function parseDefinitionValue($definition, $value)
	{
		if (is_callable($definition[self::DEFINITION_FUNC])) {
			array_unshift($definition[self::DEFINITION_ARGS], $value);
			$value = call_user_func_array($definition[self::DEFINITION_FUNC], $definition[self::DEFINITION_ARGS]);
		}

		return $value;
	}
}
