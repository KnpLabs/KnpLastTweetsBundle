# KnpLastTweetsBundles

This Symfony2 bundle will allow you to easily add a visual widget with the
last tweets of a Twitter user to your page.

Note that tweets are transformed so that links are clickable.

## Usage

After installing the bundle, just do:

```jinja
{% render "KnpLastTweetsBundle:Twitter:last" with {'username': 'knplabs'} %}
```

## Installation

Put the bundle in the `vendor/bundles/Knp/Bundle/LastTweetsBundle` dir.

If you use git submodules:

    git submodule add http://github.com/knplabs/KnpLastTweetsBundle.git vendor/bundles/Knp/Bundle/LastTweetsBundle

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
        new Knp\Bundle\MenuBundle\KnpLastTweetsBundle(),
    );
)
```

## TODO

* Cache
* Use translation in the default template
* Explain how to customize/replace the default template

## Credits

* Initial work has been done by [Knp's Symfony2 experts](http://www.knplabs.com/)
