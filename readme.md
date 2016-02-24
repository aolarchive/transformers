# Transformers, roll out!

[![Build Status](https://travis-ci.org/aol/transformers.png)](https://travis-ci.org/aol/transformers)
[![Latest Stable Version](https://poser.pugx.org/aol/transformers/v/stable.png)](https://packagist.org/packages/aol/transformers)
[![Latest Unstable Version](https://poser.pugx.org/aol/transformers/v/unstable.png)](https://packagist.org/packages/aol/transformers)
[![Total Downloads](https://poser.pugx.org/aol/transformers/downloads.png)](https://packagist.org/packages/aol/transformers)
[![Code Climate](https://codeclimate.com/github/aol/transformers/badges/gpa.svg)](https://codeclimate.com/github/aol/transformers)


## What is this?

Aol/Transformers provides a way to quickly handle two-way data transformations. This is useful for normalizing data from external or legacy systems in your application code or even for cleaning up and limiting responses from your external HTTP API.

**But why not just fix the data at the source?** If you can, do it! Often though, that's not an option, and that's when you need to "fix" the data at the application layer.

There are two terms you should know:

- **app** - Short for "application" and this is the format we want to use in our application.
- **ext** - Short for "external" and this is the format that is used externally.

## Basic usage

In a very basic use case a new Transformer instance can be created and the transformation definitions can be defined on the fly. Take a look at the code and then we'll walk thru what's happening.

```php
<?php

$post = ['Id' => '5', 'Title' => 'Awesome Post!', 'post_tags' => '["awesome"]'];

$transformer = new \Aol\Transformers\Transformer;
$transformer->define('id', 'Id', 'intval', 'strval');
$transformer->define('title', 'Title');
$transformer->define('tags', 'post_tags', 'json_decode', 'json_encode');

$post = $transformer->toApp($post);
// ['id' => 5, 'title' => 'Awesome Post!', 'tags' => ['awesome']];
```

We have a "Post" array from some external source. It could be MySql, Mongo, an API, the source doesn't really matter. The important part is that we have a post with a few issues. Here's a checklist of things we want to change:

1. All of the key names should be lowercase `snake_case`. Oh, and `post_tags` is kinda silly, we'll make that simply 'tags`.
2. The post id is always numerical, lets make that an integer.
3. The post tags are currently a JSON string, lets turn that into a PHP array.

We can make all of these changes by creating a definition for each key. The first argument is the key we want to use in the application, the second argument is the key name that has been forced upon us externally. In many cases (such as `title`) its enough to just define those two fields. 

In cases where we want to actually change the value we can pass in two more arguments. The third argument is a callable that will be applied when we convert to the application context, and the fourth argument is a callable that will be applied when we convert to the external context.


Take a look at the `define` signature:

```php
public function define($app_name, $ext_name, callable $app_func = null, callable $ext_func, $app_args = [], $ext_args = []);
```

- `$app_name` - This is the key name we want to use in the application.
- `$ext_name` - This is the key name that has been forced upon us externally.
- `$app_func` - This is an optional callable that will be applied when transforming an array to the application format.
- `$ext_func` - This is an optional callable that will be applied when transforming an array to the external format.
- `$app_args` - Optional arguments for the application callable
- `$ext_args` - Optional arguments for the external callable

## Subclass it

Often its useful to reuse a Transformer. In this create a new class that extends the Transformer class and implement the definitions in the constructor. 

```php
<?php

class Post extends \Aol\Transformers\Transformer
{
	public function __construct()
	{
		$this->define('id', 'Id', 'intval', 'strval');
		$this->define('title', 'Title');
		$this->define('tags', 'post_tags', 'json_decode', 'json_encode');
	}
}
```

## Leveraging Traits

Many common definitions can be simplified by leveraging the wrapper methods provided by utility traits. 

```php
<?php

class Post extends \Aol\Transformers\Transformer
{
	use \Aol\Transformers\Utilities\MysqlTrait,
		\Aol\Transformers\Utilities\UtilityTrait;
		
	public function __construct()
	{
		$this->defineId('id', 'Id');
		$this->define('title', 'Title');
		$this->defineJson('tags', 'post_tags');
	}
}
```

## Installation

```$ composer require aol/transformers ^2```

## Contributing
...

## License
This project is licensed under the MIT License - see the LICENSE file for details.
