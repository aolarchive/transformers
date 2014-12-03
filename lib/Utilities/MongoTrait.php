<?php

namespace Aol\Transformers\Utilities;

use MongoDate;
use MongoId;

trait MongoTrait
{
	/**
	 * This trait requires the define method.
	 *
	 * @see \Aol\Transformers\Transformer
	 */
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
	protected function defineDate($app_name, $ext_name)
	{
		$app_callable = function ($date) {
			return $date instanceof MongoDate ? new \DateTime('@' . $date->sec) : null;
		};

		$ext_callable = function ($date) {
			$date = $date instanceof \DateTime ? $date->getTimestamp() : strtotime($date);

			return new MongoDate($date);
		};

		$this->define($app_name, $ext_name, $app_callable, $ext_callable);
	}

	/**
	 * Defines an ID property.
	 *
	 * @param string $app_name Property name in application context.
	 * @param string $ext_name Property name storage context.
	 */
	protected function defineId($app_name, $ext_name)
	{
		$ext_callable = function ($id) {
			return empty($id) ? null : new MongoId($id);
		};

		$this->define($app_name, $ext_name, 'strval', $ext_callable);
	}
}
