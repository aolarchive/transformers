<?php

namespace Aol\Transformers\Utilities;

use Aol\Transformers\AbstractDefinitionTrait;

trait UtilityTrait
{
	use AbstractDefinitionTrait;

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
