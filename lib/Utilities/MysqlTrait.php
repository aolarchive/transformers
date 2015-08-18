<?php

namespace Aol\Transformers\Utilities;

use Aol\Transformers\AbstractDefinitionTrait;

trait MySqlTrait
{
	use AbstractDefinitionTrait;

	/**
	 * Defines a date property.
	 *
	 * @param string $app_name Property name in application context.
	 * @param string $ext_name Property name storage context.
	 */
	protected function defineDate($app_name, $ext_name)
	{
		$app_callable = function($date) {
			// If the value is empty, or a string containing
			// *only* characters (' ', '-', '_', ':', '\', '/', '0'), then the DateTime is considered null.
			if (empty($date) || (is_string($date) && preg_match('~^[ \\-_:\\\\/0]+$~', $date))) {
				return null;
			} else {
				return new \DateTime($date);
			}
		};

		$ext_callable = function($date) {
			return !$date instanceof \DateTime ? null : $date->format('Y-m-d');
		};

		$this->define($app_name, $ext_name, $app_callable, $ext_callable);
	}

	/**
	 * Defines a date property.
	 *
	 * @param string $app_name Property name in application context.
	 * @param string $ext_name Property name storage context.
	 */
	protected function defineDateTime($app_name, $ext_name)
	{
		$app_callable = function($date) {
			// If the value is empty, or a string containing
			// *only* characters (' ', '-', '_', ':', '\', '/', '0'), then the DateTime is considered null.
			if (empty($date) || (is_string($date) && preg_match('~^[ \\-_:\\\\/0]+$~', $date))) {
				return null;
			} else {
				return new \DateTime($date);
			}
		};

		$ext_callable = function($date) {
			return !$date instanceof \DateTime ? null : $date->format('Y-m-d H:i:s');
		};

		$this->define($app_name, $ext_name, $app_callable, $ext_callable);
	}

	/**
	 * Defines an ID property.
	 *
	 * @param string $app_name Property name in application context.
	 * @param string $ext_name Property name storage context.
	 */
	public function defineId($app_name, $ext_name)
	{
		$this->define($app_name, $ext_name, 'intval', 'strval');
	}
}
