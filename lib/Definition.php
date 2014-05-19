<?php

namespace Amp\Transformers;

class Definition
{
	private $names;
	private $funcs;

	public function __construct($app_name, $storage_name, $app_func = null, $storage_func = null)
	{
		$this->names = [
			Transformer::ENV_APP     => $app_name,
			Transformer::ENV_STORAGE => $storage_name
		];
		$this->funcs = [
			Transformer::ENV_APP     => (is_array($app_func) ? $app_func : [$app_func]) ? : [],
			Transformer::ENV_STORAGE => (is_array($storage_func) ? $storage_func : [$storage_func]) ? : []
		];
	}

	public function getKey($env)
	{
		if (!in_array($env, [Transformer::ENV_APP, Transformer::ENV_STORAGE])) {
			throw new \InvalidArgumentException;
		}

		return $this->names[$env];
	}

	public function getFunc($env)
	{
		if (!in_array($env, [Transformer::ENV_APP, Transformer::ENV_STORAGE])) {
			throw new \InvalidArgumentException;
		}

		return $this->funcs[$env];
	}
}
