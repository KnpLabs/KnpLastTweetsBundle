<?php

$vendorDir = __DIR__ . '/../vendor';
 
if (!@include($vendorDir . '/autoload.php')) {
    die("You must set up the project dependencies, run the following commands:
wget http://getcomposer.org/composer.phar
php composer.phar install --dev
");
}
 
spl_autoload_register(function($class) {
    if (0 === (strpos($class, 'Knp\\Bundle\\LastTweetsBundle\\'))) {
        $path = __DIR__.'/../'.implode('/', array_slice(explode('\\', $class), 3)).'.php';
 
        if (!stream_resolve_include_path($path)) {
            return false;
        }
        require_once $path;
        return true;
    }
});
