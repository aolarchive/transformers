<?php

namespace Aol\Transformers;

use Aura\Di\Container;

class Config extends \Aura\Di\Config
{
	public function define(Container $di)
	{
		$di->set('Aol\\Transformers\\DataStore\\Utility\\Mongo', $di->lazyNew('Aol\\Transformers\\DataStore\\Utility\\Mongo'));
		$di->set('Aol\\Transformers\\DataStore\\Utility\\Mysql', $di->lazyNew('Aol\\Transformers\\DataStore\\Utility\\Mysql'));
		$di->set('Aol\\Transformers\\Transformer', $di->lazyNew('Aol\\Transformers\\Transformer'));
		$di->set('Aol\\Transformers\\Utility', $di->lazyNew('Aol\\Transformers\\Utility'));

		$di->params['Aol\\Transformers\\Transformer']['utility'] = $this->lazyGet('Aol\\Transformers\\Utility');
	}
}
