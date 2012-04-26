<?php

namespace Knp\Bundle\LastTweetsBundle\Tests\DependencyInjection;

use Knp\Bundle\LastTweetsBundle\DependencyInjection\KnpLastTweetsExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class KnpLastTweetsExtensionTest extends \PHPUnit_Framework_TestCase
{
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
    public function shouldWorkWithZend()
    {
        $extension = $this->getExtensionMock();

        $config = $this->getConfig('zend');
        $container = new ContainerBuilder();

        $extension->load($config, $container);
        $this->assertTrue($container->hasDefinition('knp_last_tweets.last_tweets_fetcher.zend_cache'));
        $this->assertTrue($container->hasAlias('knp_last_tweets.last_tweets_additional_fetcher'));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
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
     * @expectedException \InvalidArgumentException
     */
    public function shouldNotWorkWithoutOauth()
    {
        $extension = $this->getExtensionMock();

        $extension->expects($this->once())
            ->method('isOauthExists')
            ->will($this->returnValue(false));

        $config = $this->getConfig('oauth');
        $container = new ContainerBuilder();

        $extension->load($config, $container);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function shouldNotWorkWithZendWithoutOauth()
    {
        $extension = $this->getExtensionMock();

        $extension->expects($this->once())
            ->method('isOauthExists')
            ->will($this->returnValue(false));

        $config = $this->getConfig('zend_oauth');
        $container = new ContainerBuilder();

        $extension->load($config, $container);
    }

    protected function getExtensionMock()
    {
        return $this->getMockBuilder('Knp\\Bundle\\LastTweetsBundle\\DependencyInjection\\KnpLastTweetsExtension')
            ->disableOriginalConstructor()
            ->setMethods(array('isOauthExists'))
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
