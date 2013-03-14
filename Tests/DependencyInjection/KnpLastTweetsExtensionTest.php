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

        $config = $this->getConfig('oauth');
        $container = new ContainerBuilder();

        $extension->load($config, $container);

        $this->assertTrue($container->hasAlias('knp_last_tweets.last_tweets_fetcher'));
        $this->assertTrue($container->hasDefinition('knp_last_tweets.last_tweets_fetcher.oauth'));
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
     * @expectedException \InvalidArgumentException
     */
    public function shouldNotWorkWithBadDriver()
    {
        $extension = $this->getExtensionMock();

        $config = $this->getConfig('bad');
        $container = new ContainerBuilder();

        $extension->load($config, $container);
    }

    protected function getExtensionMock()
    {
        return $this->getMockBuilder('Knp\\Bundle\\LastTweetsBundle\\DependencyInjection\\KnpLastTweetsExtension')
            ->disableOriginalConstructor()
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
        }

        return $config;
    }
}
