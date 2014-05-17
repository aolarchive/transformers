<?php

namespace Amp;

class Transformer
{
	/** @var \Amp\Transformer\Definition[] Transformation definitions */
	private $definitions = [];

	private $indexs = [];

	public function define($app_name, $storage_name, callable $app_func = null, callable $storage_func = null)
	{
		$arr   = [
			'app_name'     => $app_name,
			'storage_name' => $storage_name,
			'app_func'     => $app_func,
			'storage_func' => $storage_func
		];
		$index = array_push($this->definitions, $arr) - 1;

		$this->indexs['storage'][$app_name] = $index;
		$this->indexs['app'][$storage_name] = $index;
	}

	public function forStorage($data)
	{
		return $this->forEnv('storage', $data);
	}

	public function forApp($data)
	{
		return $this->forEnv('app', $data);
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

			if (!is_null($definition[$env . '_func'])) {
				$value = call_user_func($definition[$env . '_func'], $value);
			}

			$ret[$definition[$env . '_name']] = $value;
		}

		return $ret;
	}
}
