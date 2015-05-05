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
// ['id' => 5, 'title' => 'Awesome Post!', 'post_tags' => ['awesome']];

?>
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
?>
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
?>
```

## Installation

```$ composer require aol/transformers```

## Contributing

## License




So you have a legacy database (or external service or really any type of persistence layer) that uses some ridiculous naming scheme or data serialization process that is impossible to use or remember. In an ideal world you would go back to the source and fix the problem, but we live in a world of duct tape and krazy glue. This package aims to provide a flexible translation layer for normalizing property names and values between your app and an external data store.

```php
<?php

$transform = new \Aol\Transformers\Transformer(new \Aol\Transformers\Utility);
```

## Lets start at the end

Sometimes its best to start at the end. Let start by creating a Transformer object for a post.

```php
<?php

namespace Acme\Package;

class PostTransformer extends \Aol\Transformers\Transformer
{
    use \Aol\Transformers\DataStore\DefinitionsTrait;

    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';

    private $status_mask = [
        1 => self::STATUS_DRAFT,
        2 => self::STATUS_PUBLISHED,
    ];

    public function definitions()
    {
        
        $this->defineId('id', 'id');
        $this->defineId('user_id', 'UserID');
        
        $this->define('title', 'Title');
        $this->define('time', 'timeToRead', 'floatval');
        
        $this->defineBitmask('status', 'status', $this->status_mask);
        $this->defineDateTime('created', 'entryDate');            
        $this->defineJson('tags', 'tags');
    }
}
```

The first argument for any definition is the app name (the name you want to use) and the second is its external name. For properties like "Title" you often just need to normalize the name. Other times its nice to typecast your data so that IDs will always be ints, for example.  You can also do more complex transformations like transform a JSON string to a PHP array for easy manipulation, or map integer values to application constants in your app. And of course it is just as easy to transform everything back to its external format when you need to send it back. Lets look at an example.

```php
<?php

$utility = new \Aol\Transformers\DataStore\Utility\Mysql;
$transformer = new \Acme\Package\PostTransformer($utility);

// Typically the would be fetched from something like mysql
$post = [
    'id' => '5',
    'UserID' => '100',
    'Title' => 'Example post',
    'timeToRead' => '5.30',
    'entryDate' => '2014-02-14 13:05:22',
    'tags' => '["exciting","tags","yay"]',
    'Status' => 2
];

$post = $transformer->toApp($post);
var_dump($post);
// [
//     'id' => 5,
//     'user_id' => 100,
//     'title' => 'Example post',
//     'time' => 5.30,
//     'created' => class DateTime#1 (3) {
//         public $date => string(19) "2014-02-14 13:05:22"
//         public $timezone_type => int(3)
//         public $timezone => string(16) "America/New_York"
//     },
//     'tags' => ['exciting', 'tags', 'yay'],
//     'status' => 'published',
// ]
```

As you can see, all of the names have been changed to use a consistent format and most of our values now have specific types that we can use and manipulate in our app. You'll notice we used a very specific Utility class for Mysql. There is also a utility class for handling Mongo and a common interface that both of these classes implement. This allows you to define the transformation you need without needing to remember specific transformation methods for each storage type. The interface also makes it easy for you to add your own Utility class as a drop in replacement.

```php
<?php

$post['time'] = 6.00;
$post['title'] = 'A Different Example';

$post = $transformer->toExt($post);
var_dump($post);
// [
//     'id' => '5',
//     'UserID' => '100',
//     'Title' => 'Example post',
//     'timeToRead' => '5.30',
//     'entryDate' => '2014-02-14 13:05:22',
//     'tags' => '["exciting","tags","yay"]',
//     'Status' => 2
// ];
```

You can transform individual values on the fly.

```php
<?php

$status = $transformer->toExt('published');
echo $status;
// 2
```

And there is a third argument for handling arrays.

```php
<?php

$statuses = [1,2];
$statuses = $transformer->toApp($statuses, 'status', true);
```

## Basic Definitions

### define
All other definitions are merely convience wrappers around this core method.

```php
<?php

/**
 * Saves field definitions.
 *
 * @param string   $app_name Property name in application context.
 * @param string   $ext_name Property name storage context.
 * @param callable $app_func [Optional] Callable for transforming property to app context.
 * @param callable $ext_func [Optional] Callable for transforming property to storage context.
 * @param array    $app_args [Optional] Arguments for app callback.
 * @param array    $ext_args [Optional] Arguments for storage callback.
 */
public function define(
    $app_name,
    $ext_name,
    callable $app_func = null,
    callable $ext_func = null,
    $app_args = [],
    $ext_args = []
);
```

The first two arguments are required and simply define the property names for use in your application and externally. The second two arguments take callbacks that are used when converting to app and ext, respectively. The value will always be passed as the first argument and additional arguments can be passed by adding them as the last set of arguments.

### defineJson
This definition stores the data as a json string and decodes it as an array for the app. 

```php
<?php

$transformer->define('metadata', 'metadata');
```

This is a good example of passing additional arguments for the callback. If you were to do this manually it would look like this:

```php
<?php

$transformer->define($app_name, $storage_name, 'json_decode', 'json_encode', [true]);
```

Whenever you call `toExt($value)` it just calls `json_encode($value)`. However, when you call `toApp($value)` it will pass `true` as the second parameter `json_decode($value, true)` so that it will return a multidimensional array instead of an object.

### defineMask
This method allows you to create a map for transforming values. This is typically used for storing standardized values as `int`s and expanding them to strings (or better yet constants) for use in your app. This is particularly useful when those values are going to be exposed via an API and should be human readable.

```php
<?php

$type_mask = [
    1 => 'post',
    2 => 'page'
];

$this->defineMask('type', 'type', $type_mask);
```

## DataStore Definitions
These definitions require a utility method that implements `Aol\Transformers\DataStore\UtilityInterface` and can be added to your transformer class by using the `Aol\Transformers\DataStore\DefinitionsTrait`. All behaviors will differ by Utility class, but the basic purpose and some Mysql and Mongo use cases will be shown below.

```php
<?php

class AcmeTransformer extends Transformer
{
    use Aol\Transformers\DataStore\DefinitionsTrait;
}

$utility = new \Aol\Transformers\DataStore\Utility\Mongo;
$transformer = new AcmeTransformer($utility);
```

### defineId
This definition leverages the `idToApp` and `idToExt` methods of the utility class.

* Mongo - toApp transforms to string, toExt transforms to MongoId object.
* Mysql - toApp transforms to int, toExt transforms to string.

### defineDate
This definition leverages the `dateToApp` and `dateToExt` methods of the utility class.

* Mongo - toApp transforms to DateTime object, toExt converts to MongoDate object
* Mysql - toApp transforms to DateTime object, toExt converts to `YYYY-MM-DD`

### defineDateTime
This definition leverages the `dateToApp` and `dateToExt` methods of the utility class.

* Mongo - toApp transforms to DateTime object, toExt converts to MongoDate object
* Mysql - toApp transforms to DateTime object, toExt converts to `YYYY-MM-DD HH:MM:SS`
