<?php

require_once $_SERVER['SYMFONY'].'/Symfony/Component/ClassLoader/UniversalClassLoader.php';

$loader = new Symfony\Component\ClassLoader\UniversalClassLoader();
$loader->registerNamespace('Knp\\Bundle\\LastTweetsBundle', __DIR__.'/../../../..');
$loader->registerNamespace('Symfony', $_SERVER['SYMFONY']);
$loader->register();
