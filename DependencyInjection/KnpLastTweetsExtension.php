<?php

namespace Knp\Bundle\LastTweetsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KnpLastTweetsExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Load twig
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('twig.yml');

        // Try to load Buzz service if not found
        if (!$container->hasDefinition('buzz')) {
            $loader->load('buzz.yml');
        }

        // Load the good fetcher driver
        $fetcherConfig = isset($config['fetcher']) ? $config['fetcher'] : array();

        $driver = 'api';

        if (isset($fetcherConfig['driver'])) {
            $driver = strtolower($fetcherConfig['driver']);
        }

        if (!in_array($driver, array('api', 'zend_cache', 'array'))) {
            throw new \InvalidArgumentException('Invalid knp_last_tweets driver specified');
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/fetcher_driver'));
        $loader->load($driver.'.yml');

        if ('zend_cache' === $driver) {
            $driverOptions = array();
            if (isset($fetcherConfig['options'])) {
                $driverOptions = $fetcherConfig['options'];
            }
            if (!empty($driverOptions['cache_name'])) {
                $container->setParameter('knp_last_tweets.last_tweets_fetcher.zend_cache.cache_name', $driverOptions['cache_name']);
            }
        }

        $container->setAlias('knp_last_tweets.last_tweets_fetcher', 'knp_last_tweets.last_tweets_fetcher.'.$driver);
    }
}
