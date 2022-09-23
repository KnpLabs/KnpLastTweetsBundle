# DEPRECATED
Unfortunately we decided to not maintain this project anymore ([see why](https://knplabs.com/en/blog/news-for-our-foss-projects-maintenance)).
If you want to mark another package as a replacement for this one please send an email to [hello@knplabs.com](mailto:hello@knplabs.com).

# KnpLastTweetsBundles

## Warning

the bundle is working only with OAuth driver because of [twitter api v.1 retirement](https://blog.twitter.com/2013/api-v1-is-retired)
old users needs update ``abraham/twitteroauth`` using ``composer update abraham/twitteroauth``


This Symfony2 bundle will allow you to easily add a visual widget with the
last tweets of the Twitter users to your page.

[![Build Status](https://secure.travis-ci.org/KnpLabs/KnpLastTweetsBundle.png?branch=master)](http://travis-ci.org/KnpLabs/KnpLastTweetsBundle)

Note that tweets are transformed so that links are clickable.

## Installation

Add KnpLastTweetsBundle in your composer.json

```js
{
    "require": {
        "knplabs/knp-last-tweets-bundle": "*"
    }
}
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

### OAuth driver

The `oauth_driver` uses [InoriTwitterAppBundle](https://github.com/Inori/InoriTwitterAppBundle/blob/master/README.md).
First you should configure and install it.

Then you are freely to set it in config:
```jinja
# app/config.yml
knp_last_tweets:
    fetcher:
        driver: oauth
```

### Doctrine Cache driver

The `doctrine_driver` uses [DoctrineCache](https://github.com/doctrine/cache).
First you should configure and install it.

Then you are freely to set it in config:
```jinja
# app/config.yml
knp_last_tweets:
    fetcher:
        driver: doctrine_cache
        options:
            cache_service: my_doctrine_cache_service #must be a valid doctrine cache
```

you could use [LiipDoctrineCacheBundle](https://github.com/liip/LiipDoctrineCacheBundle) for configuring your caches.

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
            method: api # or oauth
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
* Use the `zend_cache` or `doctrine_cache` driver in production (edit your `app/config.yml` file)
* Use the `force-fetch` command in a cron job in production
* Use HTTP caching if you know what this is about and if performance is really important to you!
* Use the `oauth` driver if you have problems with limits.

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
{% render "KnpLastTweetsBundle:Twitter:lastTweets" with {'username': ['knplabs', 'knplabsru'], 'age': 5}, {'standalone': true} %}
```

## Credits

* Initial work has been done by [KnpLabs](http://knplabs.com/)

## License

`KnpLastTweetsBundle` is released under the MIT License. See the bundled LICENSE file for details.
