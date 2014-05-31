<?php

namespace Amp\Transformers\DataStore;

use Amp\Transformers\Exception;

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
		$this->validateUtilityInterface();
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
		$this->validateUtilityInterface();
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
		$this->validateUtilityInterface();
		$this->define($app_name, $ext_name, [$this->utility, 'idToApp'], [$this->utility, 'idToExt']);
	}

	private function validateUtilityInterface()
	{
		$interface = 'Amp\\Transformers\\DataStore\\UtilityInterface';
		if (!$this->utility instanceof $interface) {
			throw new Exception('This method requires the Utility class to implement ' . $interface);
		}
	}
}
