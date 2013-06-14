<?php

namespace Knp\Bundle\LastTweetsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
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
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('helper.yml');
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

        if (!in_array($driver, array('oauth', 'api', 'zend_cache', 'array', 'doctrine_cache'))) {
            throw new InvalidConfigurationException('Invalid knp_last_tweets driver specified');
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/fetcher_driver'));
        $loader->load($driver . '.yml');

        switch ($driver) {
            case 'oauth' :
                if (!$this->oauthExists()) {
                    throw new InvalidConfigurationException('oauth fetcher requires "inori/twitter-app-bundle"');
                }
                break;
            case 'zend_cache' :
                if (!$this->zendCacheExists()) {
                    throw new InvalidConfigurationException('zend_cache fetcher needs that you install "knplabs/knp-zend-cache-bundle"');
                }

                $driverOptions = $this->setRealFetcherForCacheFetcher($container, $fetcherConfig, $loader);

                if (!empty($driverOptions['cache_name'])) {
                    $container->setParameter('knp_last_tweets.last_tweets_fetcher.zend_cache.cache_name', $driverOptions['cache_name']);
                }
                break;
            case 'doctrine_cache' :
                if (!$this->doctrineCacheExists()) {
                    throw new InvalidConfigurationException('doctrine_cache fetcher needs that you install "doctrine/cache"');
                }

                $driverOptions = $this->setRealFetcherForCacheFetcher($container, $fetcherConfig, $loader);

                if (!empty($driverOptions['cache_service'])) {
                    $definition = $container->getDefinition('knp_last_tweets.last_tweets_fetcher.doctrine_cache');
                    $definition->addArgument(new Reference($driverOptions['cache_service']));
                } else {
                    throw new InvalidConfigurationException('you must specify the "cache_service" key under "options" which should point to a valid doctrine cache');
                }
                break;
        }

        $container->setAlias('knp_last_tweets.last_tweets_fetcher', 'knp_last_tweets.last_tweets_fetcher.' . $driver);
    }

    protected function oauthExists()
    {
        return class_exists('Inori\TwitterAppBundle\Services\TwitterApp');
    }

    protected function doctrineCacheExists()
    {
        return class_exists('Doctrine\Common\Cache\Cache');
    }

    protected function zendCacheExists()
    {
        return class_exists('Zend\Cache\Manager');
    }

    private function setRealFetcherForCacheFetcher(ContainerBuilder $container, $fetcherConfig, LoaderInterface $loader)
    {
        $driverOptions = array();
        if (isset($fetcherConfig['options'])) {
            $driverOptions = $fetcherConfig['options'];

            if (isset($driverOptions['method'])) {
                if (!in_array($driverOptions['method'], array('oauth', 'api'))) {
                    throw new InvalidConfigurationException('Invalid API driver specified (' . $driverOptions['method'] . '), available are: "oauth", "api"');
                }
                if (!$this->oauthExists()) {
                    throw new InvalidConfigurationException('oauth fetcher requires "inori/twitter-app-bundle"');
                }

                $loader->load($driverOptions['method'] . '.yml');

                $container->setAlias('knp_last_tweets.last_tweets_additional_fetcher', 'knp_last_tweets.last_tweets_fetcher.' . $driverOptions['method']);
            } else {
                $container->setAlias('knp_last_tweets.last_tweets_additional_fetcher', 'knp_last_tweets.last_tweets_fetcher.api');
            }
        }

        return $driverOptions;
    }
}
