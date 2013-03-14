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
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('helper.yml');
        $loader->load('twig.yml');
        $loader->load('buzz.yml');

        // Load the good fetcher driver
        $fetcherConfig = isset($config['fetcher']) ? $config['fetcher'] : array();

        $driver = $fetcherConfig['driver'];
        if (
            ('oauth' === $fetcherConfig['driver'] || (isset($fetcherConfig['options']['method']) && 'oauth' === $fetcherConfig['options']['method']))
            &&
            !class_exists('Inori\TwitterAppBundle\Services\TwitterApp')
        ) {
            throw new \InvalidArgumentException('You must install and enable the "InoriTwitterBundle" first!');
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/fetcher_driver'));
        $loader->load($driver.'.yml');

        if (isset($fetcherConfig['cache'])) {
        }

        $container->setAlias('knp_last_tweets.last_tweets_fetcher', 'knp_last_tweets.last_tweets_fetcher.' . $driver);
    }
}
