<?php

namespace Amp;

use Amp\Transformer\Definition;

class Transformer
{
	const ENV_STORAGE = 'storage';
	const ENV_APP = 'app';

	/** @var \Amp\Transformer\Definition[] Transformation definitions */
	private $definitions = [];

	private $indexs = [];

	public function define($app_name, $storage_name, callable $app_func = null, callable $storage_func = null)
	{
		$definition = new Definition($app_name, $storage_name, $app_func, $storage_func);
		$index      = array_push($this->definitions, $definition) - 1;

		$this->indexs[self::ENV_STORAGE][$app_name] = $index;
		$this->indexs[self::ENV_APP][$storage_name] = $index;
	}

	public function forStorage($data)
	{
		return $this->forEnv(self::ENV_STORAGE, $data);
	}

	public function forApp($data)
	{
		return $this->forEnv(self::ENV_APP, $data);
	}

	private function forEnv($env, $data)
	{
		$ret = [];
		foreach ($data as $key => $value) {
			if (!isset($this->indexs[$env][$key])) {
				continue;
			}

			$index      = $this->indexs[$env][$key];
			$definition = $this->definitions[$index];

			$ret[$definition->getKey($env)] = $definition->parseValue($env, $value);
		}

		return $ret;
	}
}
