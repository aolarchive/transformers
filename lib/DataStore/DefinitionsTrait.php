<?php

namespace Amp\Transformers\DataStore;

trait DefinitionsTrait
{
	/** @var \Amp\Transformers\DataStore\UtilityInterface Utility object. */
	protected $utility;

	abstract public function define(
		$app_name,
		$ext_name,
		callable $app_func = null,
		callable $ext_func = null,
		$app_args = [],
		$ext_args = []
	);

	/**
	 * Defines a date property.
	 *
	 * @param string $app_name Property name in application context.
	 * @param string $ext_name Property name storage context.
	 */
	public function defineDate($app_name, $ext_name)
	{
		$this->define($app_name, $ext_name, [$this->utility, 'dateToApp'], [$this->utility, 'dateToExt']);
	}

	/**
	 * Defines a date time property.
	 *
	 * @param string $app_name Property name in application context.
	 * @param string $ext_name Property name storage context.
	 */
	public function defineDateTime($app_name, $ext_name)
	{
		$this->define($app_name, $ext_name, [$this->utility, 'datetimeToApp'], [$this->utility, 'datetimeToExt']);
	}

	/**
	 * Defines an ID property.
	 *
	 * @param string $app_name Property name in application context.
	 * @param string $ext_name Property name storage context.
	 */
	public function defineId($app_name, $ext_name)
	{
		$this->define($app_name, $ext_name, [$this->utility, 'idToApp'], [$this->utility, 'idToExt']);
	}
}
