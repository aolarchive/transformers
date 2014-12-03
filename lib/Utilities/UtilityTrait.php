<?php

namespace Aol\Transformers\Utilities;

trait UtilityTrait
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
	 * Defines a property that is stored as JSON. Expands to an array in app.
	 *
	 * @param string $app_name     Property name in application context.
	 * @param string $storage_name Property name storage context.
	 */
	protected  function defineJson($app_name, $storage_name)
	{
		$this->define($app_name, $storage_name, 'json_decode', 'json_encode', [true]);
	}

	/**
	 * @param string $app_name     Property name in application context.
	 * @param string $storage_name Property name storage context.
	 * @param array  $mask
	 */
	protected  function defineMask($app_name, $storage_name, $mask)
	{
		$mask_flip = array_flip($mask);
		$bitmask = function($value, $mask) {
			return isset($mask[$value]) ? $mask[$value] : null;
		};

		$this->define(
			$app_name,
			$storage_name,
			$bitmask,
			$bitmask,
			[$mask],
			[$mask_flip]
		);
	}
}
