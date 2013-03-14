<?php

namespace Knp\Bundle\LastTweetsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public static $drivers   = array('oauth', 'api', 'array');
    public static $providers = array(
        'doctrine_apc',
        'doctrine_array',
        'doctrine_couchbase',
        'doctrine_filesystem',
        'doctrine_memcache',
        'doctrine_memcached',
        'doctrine_phpfile',
        'doctrine_redis',
        'doctrine_wincache',
        'doctrine_xcache',
        'doctrine_zenddata',

        'zf2'
    );

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('knp_last_tweets');

        $rootNode
            ->children()
                ->arrayNode('fetcher')
                ->treatNullLike(array('driver' => 'api'))
                ->treatTrueLike(array('driver' => 'api'))
                ->children()
                    ->scalarNode('driver')
                        ->defaultValue('api')
                        ->validate()
                            ->ifTrue(function($driver) {
                                if (empty($driver)) {
                                    return true;
                                }

                                return in_array(strtolower($driver), self::$drivers);
                            })
                            ->thenInvalid('Unknown driver specified: "%s".')
                        ->end()
                    ->end()
                    ->arrayNode('cache')
                        ->addDefaultsIfNotSet()
                        ->useAttributeAsKey('provider')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('provider')
                                    ->defaultNull()
                                    ->validate()
                                        ->ifTrue(function($provider) {
                                            if (empty($provider)) {
                                                return false;
                                            }

                                            return in_array(strtolower($provider), self::$providers);
                                        })
                                        ->thenInvalid('Unknown cache provider specified: "%s".')
                                    ->end()
                                ->end()
                                ->scalarNode('namespace')
                                    ->cannotBeEmpty()
                                    ->defaultValue('api')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('options')->useAttributeAsKey(0)->prototype('scalar')->end()
                ->end()
            ->end()
            ;

        return $treeBuilder;
    }
}
