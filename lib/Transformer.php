<?php

namespace Amp;

class Transformer
{
    /** @var array Transformation definitions */
    private $definitions = [];

    private $index_app_names = [];
    private $index_storage_names = [];

    public function define($app_name, $storage_name, callable $app_func = null, callable $storage_func = null)
    {
        $arr = [
            'app_name'     => $app_name,
            'storage_name' => $storage_name,
            'app_func'     => $app_func,
            'storage_func' => $storage_func
        ];
        $index = array_push($this->definitions, $arr) - 1;

        $this->index_app_names[$app_name] = $index;
        $this->index_storage_names[$storage_name] = $index;
    }

    public function toStorage($data)
    {
        $ret = [];
        foreach ($data as $key => $value) {
            if (!isset($this->index_app_names[$key])) {
                continue;
            }

            $index = $this->index_app_names[$key];
            $definition = $this->definitions[$index];

            if(!is_null($definition['storage_func'])) {
                $value = call_user_func($definition['storage_func'], $value);
            }

            $ret[$definition['storage_name']] = $value;
        }

        return $ret;
    }

    public function toApp($data)
    {
        $ret = [];
        foreach ($data as $key => $value) {
            if (!isset($this->index_storage_names[$key])) {
                continue;
            }

            $index = $this->index_storage_names[$key];
            $definition = $this->definitions[$index];

            if(!is_null($definition['app_func'])) {
                $value = call_user_func($definition['app_func'], $value);
            }

            $ret[$definition['app_name']] = $value;
        }

        return $ret;
    }
}
