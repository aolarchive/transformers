<?php

namespace Amp\Transformers\DataStore;

use Amp\Transformers\Exception;

trait DefinitionsTrait
{
	abstract public function define(
		$app_name,
		$ext_name,
		callable $app_func = null,
		callable $ext_func = null,
		$app_args = [],
		$ext_args = []
	);

	/**
	 * @return \Amp\Transformers\DataStore\UtilityInterface
	 */
	abstract protected function getUtility();

	/**
	 * Defines a date property.
	 *
	 * @param string $app_name Property name in application context.
	 * @param string $ext_name Property name storage context.
	 */
	public function defineDate($app_name, $ext_name)
	{
		$this->validateUtilityInterface();
		$this->define($app_name, $ext_name, [$this->getUtility(), 'dateToApp'], [$this->getUtility(), 'dateToExt']);
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
		$this->define($app_name, $ext_name, [$this->getUtility(), 'datetimeToApp'], [$this->getUtility(), 'datetimeToExt']);
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
		$this->define($app_name, $ext_name, [$this->getUtility(), 'idToApp'], [$this->getUtility(), 'idToExt']);
	}

	private function validateUtilityInterface()
	{
		if (!$this->getUtility() instanceof UtilityInterface) {
			throw new Exception('This method requires the Utility class to implement ' . $interface);
		}
	}
}
