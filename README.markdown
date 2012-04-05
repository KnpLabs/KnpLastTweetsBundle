# KnpLastTweetsBundles

This Symfony2 bundle will allow you to easily add a visual widget with the
last tweets of the Twitter users to your page.

Note that tweets are transformed so that links are clickable.

## Usage

After installing the bundle, just do:

```jinja
{% render "KnpLastTweetsBundle:Twitter:lastTweets" with {'username': 'knplabs'} %}
```

Or if you want use combined feed:

```jinja
{% render "KnpLastTweetsBundle:Twitter:lastTweets" with {'username': ['knplabs', 'knplabsru']} %}
```

In that case tweets will be sorted by date.

## Installation

Put the bundle in the `vendor/bundles/Knp/Bundle/LastTweetsBundle` dir.

If you use git submodules:

    git submodule add http://github.com/KnpLabs/KnpLastTweetsBundle.git vendor/bundles/Knp/Bundle/LastTweetsBundle

Register the `Knp/Bundle` namespace in your `autoload.php`

```php
<?php

$loader->registerNamespaces(array(
    // ...
    'Knp\\Bundle'                    => __DIR__.'/../vendor/bundles',
));
```

Register the bundle in your `app/AppKernel.php`:

```php
<?php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Knp\Bundle\LastTweetsBundle\KnpLastTweetsBundle(),
    );
)
```

Buzz is required to use this bundle.

You should configure it in config.yml by adding

```jinja
# app/config.yml
services:
    buzz.message_factory:
        class: Buzz\Message\Factory
        public: false

    buzz.client:
        class: Buzz\Client\Curl
        public: false

    buzz:
        class: Buzz\Browser
        arguments: ["@buzz.client", "@buzz.message_factory"]
```

## Configuration

You will now have to configure the bundle to use one of the three available drivers.

### Api driver

The simplest driver is the `api` driver: it calls twitter API at each request.

```jinja
# app/config.yml
knp_last_tweets:
    fetcher:
        driver: api
```

This is the default - you don't even have to add the previous config to `app/config.yml`.  
But it's obviously not peformant in production.

### Zend_Cache driver

The `zend_cache` driver uses Zend_Cache to cache the last tweets in a Zend_Cache_Backend (file, APC, memcached…).

You will need to install [KnpZendCacheBundle](https://github.com/KnpLabs/KnpZendCacheBundle) first
and configure it:

```jinja
# app/config.yml
knp_zend_cache:
    templates:
        knp_last_tweets:
            frontend:
                name: Core
                options:
                    lifetime: 300
                    automatic_serialization: true
            backend:
                name: File
                options:
                    cache_dir: %kernel.root_dir%/cache/%kernel.environment%

knp_last_tweets:
    fetcher:
        driver: zend_cache
        options:
            cache_name: knp_last_tweets
```

This will only call the twitter api after a minimum of 300 seconds.

#### The force-fetch command

Caching is good. But once in a while (well one every 5 minutes in the previous case and if you have a continuous flow a visits), one of your visitor will have to wait 2 unnecessary seconds while the server calls twitter API.

To avoid that, you should launch a cron job every 4 minutes which will force the fetching and caching of the new tweets

    php app/console knp-last-tweets:force-fetch knplabs

This way, you will never make your visitors wait!

### Array driver

The `array` driver uses dummy data and does not call the twitter API.

It will return you 10 fake tweets - perfect in development.

```jinja
# app/config.yml
knp_last_tweets:
    fetcher:
        driver: array
```

### Recommendations

* Use the `array` driver in development (edit your `app/config_dev.yml` file)
* Use the `zend_cache` driver in production (edit your `app/config.yml` file)
* Use the `force-fetch` command in a cron job in production
* Use HTTP caching if you know what this is about and if performance is really important to you!

## Advanced usage: HTTP caching

*Please note that the following is not necessary: you should be perfectly
fine without it.*

You can use [HTTP caching](http://symfony.com/doc/2.0/book/http_cache.html) 
and [ESI](http://symfony.com/doc/2.0/book/http_cache.html#using-esi-in-symfony2)
if you want the `lastTweets` action to be rendered as an ESI tag.

This will improve performance by using a cached version of the whole
rendered block - even in a dynamic page.

Follow the [instructions](http://symfony.com/doc/2.0/book/http_cache.html) on symfony.com
and use the following code in your templates:

```jinja
{% render "KnpLastTweetsBundle:Twitter:lastTweets" with {'username': 'knplabs', 'age': 5}, {'standalone': true} %}
```

## Credits

* Initial work has been done by [KnpLabs](http://knplabs.com/)
