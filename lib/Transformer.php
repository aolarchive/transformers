<?php

namespace Amp\Transformers;

class Transformer
{
	const ENV_APP = 'app';
	const ENV_STORAGE = 'storage';

	const DEFINITION_KEY = 'key';
	const DEFINITION_FUNC = 'func';
	const DEFINITION_ARGS = 'args';

	/** @var array Transformation definitions */
	private $definitions = [];

	/** @var Utility Utility object. */
	private $utility;

	/**
	 * @param Utility $utility Utility object.
	 */
	public function __construct(Utility $utility)
	{
		$this->utility = $utility;

		$this->definitions();
	}

	/**
	 * Saves field definitions.
	 *
	 * @param string   $app_name     Property name in application context.
	 * @param string   $storage_name Property name storage context.
	 * @param callable $app_func     Callable for transforming property to app context.
	 * @param callable $storage_func Callable for transforming property to storage context.
	 * @param array    $app_args     Arguments for app callback.
	 * @param array    $storage_args Arguments for storage callback.
	 */
	public function define(
		$app_name,
		$storage_name,
		callable $app_func = null,
		callable $storage_func = null,
		$app_args = [],
		$storage_args = []
	) {
		$this->definitions[self::ENV_STORAGE][$app_name] = [
			self::DEFINITION_KEY  => $storage_name,
			self::DEFINITION_FUNC => $storage_func,
			self::DEFINITION_ARGS => $storage_args
		];
		$this->definitions[self::ENV_APP][$storage_name] = [
			self::DEFINITION_KEY  => $app_name,
			self::DEFINITION_FUNC => $app_func,
			self::DEFINITION_ARGS => $app_args,
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
		$this->define($app_name, $storage_name, 'date_create', [$this->utility, 'convertDateToMysql']);
	}

	/**
	 * Defines a property that is stored as JSON. Expands to an array in app.
	 *
	 * @param string $app_name     Property name in application context.
	 * @param string $storage_name Property name storage context.
	 */
	public function defineJson($app_name, $storage_name)
	{
		$this->define($app_name, $storage_name, 'json_decode', 'json_encode', [true]);
	}

	/**
	 * @param string $app_name     Property name in application context.
	 * @param string $storage_name Property name storage context.
	 * @param array  $mask
	 */
	public function defineMask($app_name, $storage_name, $mask)
	{
		$mask_flip = array_flip($mask);

		$this->define(
			$app_name,
			$storage_name,
			[$this->utility, 'bitmask'],
			[$this->utility, 'bitmask'],
			[$mask],
			[$mask_flip]
		);
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
			if ($def = $this->getDefinition($env, $key)) {
				$ret[$def[self::DEFINITION_KEY]] = $this->parseDefinitionValue($def, $value);
			}
		}

		return $ret;
	}

	/**
	 * A no-op. Can be used in subclass to setup definitions.
	 */
	protected function definitions()
	{

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
