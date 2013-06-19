<?php

namespace Knp\Bundle\LastTweetsBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class KnpLastTweetsExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldSetAliases()
    {
        $extension = $this->getExtensionMock();

        $extension->expects($this->once())
            ->method('oauthExists')
            ->will($this->returnValue(true));
        $extension->expects($this->once())
            ->method('zendCacheExists')
            ->will($this->returnValue(true));

        $config = $this->getConfig('zend_oauth');
        $container = new ContainerBuilder();

        $extension->load($config, $container);

        $this->assertTrue($container->hasAlias('knp_last_tweets.last_tweets_fetcher'));
        $this->assertTrue($container->hasDefinition('knp_last_tweets.last_tweets_fetcher.oauth'));
        $this->assertTrue($container->hasDefinition('knp_last_tweets.last_tweets_fetcher.zend_cache'));
        $this->assertTrue($container->hasAlias('knp_last_tweets.last_tweets_additional_fetcher'));
    }

    /**
     * @test
     */
    public function shouldWorkWithArray()
    {
        $extension = $this->getExtensionMock();

        $config = $this->getConfig('array');
        $container = new ContainerBuilder();

        $extension->load($config, $container);

        $this->assertTrue($container->hasDefinition('knp_last_tweets.last_tweets_fetcher.array'));
    }

    /**
     * @test
     */
    public function shouldWorkWithDoctrineCache()
    {
        $extension = $this->getExtensionMock();

        $config = $this->getConfig('doctrine');
        $container = new ContainerBuilder();

        $extension->expects($this->once())
            ->method('doctrineCacheExists')
            ->will($this->returnValue(true));

        $extension->load($config, $container);

        $this->assertTrue($container->hasDefinition('knp_last_tweets.last_tweets_fetcher.doctrine_cache'));
    }

    /**
     * @test
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function shouldNotWorkWithoutDoctrineCache()
    {
        $extension = $this->getExtensionMock();

        $config = $this->getConfig('doctrine');
        $container = new ContainerBuilder();

        $extension->expects($this->once())
            ->method('doctrineCacheExists')
            ->will($this->returnValue(false));

        $extension->load($config, $container);
    }

    /**
     * @test
     */
    public function shouldWorkWithZendCache()
    {
        $extension = $this->getExtensionMock();

        $extension->expects($this->once())
            ->method('zendCacheExists')
            ->will($this->returnValue(true));

        $config = $this->getConfig('zend');
        $container = new ContainerBuilder();

        $extension->load($config, $container);
        $this->assertTrue($container->hasDefinition('knp_last_tweets.last_tweets_fetcher.zend_cache'));
        $this->assertTrue($container->hasAlias('knp_last_tweets.last_tweets_additional_fetcher'));
    }

    /**
     * @test
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function shouldNotWorkWithBadDriver()
    {
        $extension = $this->getExtensionMock();

        $config = $this->getConfig('bad');
        $container = new ContainerBuilder();

        $extension->load($config, $container);
    }

    /**
     * @test
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function shouldNotWorkWithoutOauth()
    {
        $extension = $this->getExtensionMock();

        $extension->expects($this->once())
            ->method('oauthExists')
            ->will($this->returnValue(false));

        $config = $this->getConfig('oauth');
        $container = new ContainerBuilder();

        $extension->load($config, $container);
    }

    /**
     * @test
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function shouldNotWorkWithZendWithoutOauth()
    {
        $extension = $this->getExtensionMock();

        $extension->expects($this->once())
            ->method('zendCacheExists')
            ->will($this->returnValue(true));

        $extension->expects($this->once())
            ->method('oauthExists')
            ->will($this->returnValue(false));

        $config = $this->getConfig('zend_oauth');
        $container = new ContainerBuilder();

        $extension->load($config, $container);
    }

    protected function getExtensionMock()
    {
        return $this->getMockBuilder('Knp\\Bundle\\LastTweetsBundle\\DependencyInjection\\KnpLastTweetsExtension')
            ->disableOriginalConstructor()
            ->setMethods(array('oauthExists', 'zendCacheExists', 'doctrineCacheExists'))
            ->getMock();
    }

    protected function getConfig($type)
    {
        switch ($type) {
            case 'array':
                $config = array(
                    'knp_last_tweets' => array(
                        'fetcher' => array(
                            'driver' => 'array'
                        )
                    )
                );
                break;

            case 'bad':
                $config = array(
                    'knp_last_tweets' => array(
                        'fetcher' => array(
                            'driver' => 'loremipsum'
                        )
                    )
                );
                break;

            case 'oauth':
                $config = array(
                    'knp_last_tweets' => array(
                        'fetcher' => array(
                            'driver' => 'oauth'
                        )
                    )
                );
                break;

            case 'zend':
                $config = array(
                    'knp_last_tweets' => array(
                        'fetcher' => array(
                            'driver' => 'zend_cache',
                            'options' => array(
                                'cache_name' => 'knp_last_tweets'
                            )
                        )
                    )
                );
                break;

            case 'doctrine':
                $config = array(
                    'knp_last_tweets' => array(
                        'fetcher' => array(
                            'driver' => 'doctrine_cache',
                            'options' => array(
                                'cache_service' => 'doctrine.cache.service'
                            )
                        )
                    )
                );
                break;

            case 'zend_oauth':
                $config = array(
                    'knp_last_tweets' => array(
                        'fetcher' => array(
                            'driver' => 'zend_cache',
                            'options' => array(
                                'cache_name' => 'knp_last_tweets',
                                'method' => 'oauth'
                            )
                        )
                    )
                );
                break;
        }

        return $config;
    }
}
