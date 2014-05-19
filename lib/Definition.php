<?php

namespace Amp\Transformers;

class Definition
{
	private $names;
	private $funcs;

	public function __construct($app_name, $storage_name, callable $app_func = null, callable $storage_func = null)
	{
		$this->names = [
			Transformer::ENV_APP     => $app_name,
			Transformer::ENV_STORAGE => $storage_name
		];
		$this->funcs = [
			Transformer::ENV_APP     => $app_func,
			Transformer::ENV_STORAGE => $storage_func
		];
	}

	public function getKey($env)
	{
		if (!in_array($env, [Transformer::ENV_APP, Transformer::ENV_STORAGE])) {
			throw new \InvalidArgumentException;
		}

		return $this->names[$env];
	}

	public function parseValue($env, $value)
	{
		if (!in_array($env, [Transformer::ENV_APP, Transformer::ENV_STORAGE])) {
			throw new \InvalidArgumentException;
		}

		if (is_callable($this->funcs[$env])) {
			$value = call_user_func($this->funcs[$env], $value);
		}

		return $value;
	}
}
