<?php

namespace Aol\Transformers;

trait AbstractDefinitionTrait
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
}