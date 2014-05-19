# Transformers, roll out!

So you have a legacy database (or external service or really any type of persistence layer) that uses some crazy naming scheme or data serialization process that is impossible to use or remember. In an ideal world you would go back to the source and fix the problem, but we live in a world of duct tape and super glue. This package aims to provide a flexible translation layer for normalizing property names and values between your app and an external data store.

    <?php

    $transform = new \Amp\Transformers\Transformer();

## Definitions
Data transformation is handled one row at a time. Lets start with an example blog post.

    <?php

    $post = [
        'id' => '5',
        'UserID' => '100',
        'entryDate' => '2014-02-14 12:00:00',
        'Title' => 'Example post',
        'tags' => '["exciting","tags","yay"]',
        'Status' => 2
    ]

The first thing you'll probably notice is that the property names are all over the place. Lets start by creating a definition for each one of those. We will use the Transformer's `define()` method passing in the normalized name first and the external name second.

    <?php

    $transform->define('id', 'id');
    $transform->define('user_id', 'UserID');
    $transform->define('created', 'entryDate');
    $transform->define('title', 'Title');
    $transform->define('tags', 'tags');
    $transform->define('status', 'Status');

With those definitions loaded in all we have to do is transform the data for our app.

    <?php

    $post = $transform->forApp($post);

    var_dump($post);
    // [
    //     'id' => '5',
    //     'user_id' => '100',
    //     'created' => '2014-02-14 12:00:00',
    //     'title' => 'Example post',
    //     'tags' => '["exciting","tags","yay"]',
    //     'status' => 2
    // ]

Alright! That's already a whole lot better. And if you need to manipulate the values and send them back its just as easy to transform them the other way.

    <?php

    $post['title'] = 'How to be awesome';
    $post = $transform->forStorage($post);

    var_dump($post);
    // [
    //     'id' => '5',
    //     'UserID' => '100',
    //     'entryDate' => '2014-02-14 12:00:00',
    //     'Title' => 'How to be awesome',
    //     'tags' => '["exciting","tags","yay"]',
    //     'Status' => 2
    // ]

Lets tackle those tags next. They are stored as JSON string so we will want to `json_decode` them for our app and then `json_encode` them again for storage.

    <?php

    $transform->define('tags', 'tags', 'json_decode', 'json_encode');
    // ...

    $post = $transform->forApp($post);

    var_dump($post);
    // [
    //     'id' => '5',
    //     'user_id' => '100',
    //     'created' => '2014-02-14 12:00:00',
    //     'title' => 'Example post',
    //     'tags' => ['exciting', 'tags', 'yay'],
    //     'status' => 2
    // ]

Booya. And transforming for storage will of course convert it back to a JSON string. How did that work though? The 3rd and 4th arguments of `define()` will accept any [PHP callable](http://www.php.net/manual/en/language.types.callable.php) and use it to parse the value. This provides a lot of flexibility for using PHP functions and methods or you can quickly create your own.

There are also some definition shortcuts built in for common needs. For example, instead of manually referencing the json functions above you can simply use `defineJson`.

    <?php

    $transform->defineJson('tags', 'tags');
    $transform->defineDate('created', 'entryDate');

That will do exactly the same thing as above, except it looks a bit prettier and saves you from having to type an additional 30 characters. You can see we also used another shortcut `defineDate`. This will take the date string and turn it into a proper DateTime object for our app and convert it back to the MySQL date format for storage.

The last thing we should address is that status field. Its fairly common to store ints instead of strings for status values, but often you will want to represent that in your app as a string. You can easily define a list of key value pairs like so:

    <?php

    $status_mask = [
        1 => 'draft',
        2 => 'published'
    ];

    $transform->defineBitmask('status', 'status', $status_mask);
