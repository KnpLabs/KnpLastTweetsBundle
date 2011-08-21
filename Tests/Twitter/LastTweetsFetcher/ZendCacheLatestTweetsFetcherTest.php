<?php

namespace Knp\Bundle\LastTweetsBundle\Tests\Twitter\LastTweetsFetcher;

use Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher\ApiLastTweetsFetcher;
use Knp\Bundle\LastTweetsBundle\Twitter\Tweet;

class ZendCacheLastTweetsFetcherTest extends \PHPUnit_Framework_TestCase
{
    const CLASSNAME = 'Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher\ZendCacheLastTweetsFetcher';

    public function testFetchCached()
    {
        $mockFetcher = $this->getMock('Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher\LastTweetsFetcherInterface');
        $mockCacheManager = $this->getMock('Zend\Cache\Manager', array('getCache'));
        $mockCache = $this->getMock('Zend\Cache\Frontend\Frontend', array('load'));
        $cacheName = 'lorem';
        $returnedTweets = array('ipsum');

        $mockCacheManager->expects($this->once())
            ->method('getCache')
            ->with($this->equalTo($cacheName))
            ->will($this->returnValue($mockCache));

        $mockCache->expects($this->once())
            ->method('load')
            ->with($this->equalTo('knp_last_tweets_knplabs_10'))
            ->will($this->returnValue($returnedTweets));

        $class = self::CLASSNAME;
        $fetcher = new $class($mockFetcher, $mockCacheManager, $cacheName);

        $tweets = $fetcher->fetch('knplabs');

        $this->assertEquals($returnedTweets, $tweets);
    }

    public function testFetchNotCached()
    {
        $mockFetcher = $this->getMock('Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher\LastTweetsFetcherInterface');
        $mockCacheManager = $this->getMock('Zend\Cache\Manager', array('getCache'));
        $mockCache = $this->getMock('Zend\Cache\Frontend\Frontend', array('load', 'save'));
        $cacheName = 'lorem';
        $cacheId = 'knp_last_tweets_knplabs_10';
        $returnedTweets = array('ipsum');

        $mockFetcher->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo('knplabs'), $this->equalTo(10))
            ->will($this->returnValue($returnedTweets));

        $mockCacheManager->expects($this->once())
            ->method('getCache')
            ->with($this->equalTo($cacheName))
            ->will($this->returnValue($mockCache));

        $mockCache->expects($this->once())
            ->method('load')
            ->with($this->equalTo($cacheId))
            ->will($this->returnValue(false));

        $mockCache->expects($this->once())
            ->method('save')
            ->with($this->equalTo($returnedTweets), $this->equalTo($cacheId));

        $class = self::CLASSNAME;
        $fetcher = new $class($mockFetcher, $mockCacheManager, $cacheName);

        $tweets = $fetcher->fetch('knplabs');

        $this->assertEquals($returnedTweets, $tweets);
    }
    
    public function testFetchForceRefresh()
    {
        $mockFetcher = $this->getMock('Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher\LastTweetsFetcherInterface');
        $mockCacheManager = $this->getMock('Zend\Cache\Manager', array('getCache'));
        $mockCache = $this->getMock('Zend\Cache\Frontend\Frontend', array('save'));
        $cacheName = 'lorem';
        $returnedTweets = array('ipsum');

        $mockFetcher->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo('knplabs'), $this->equalTo(10))
            ->will($this->returnValue($returnedTweets));

        $mockCacheManager->expects($this->once())
            ->method('getCache')
            ->with($this->equalTo($cacheName))
            ->will($this->returnValue($mockCache));

        $class = self::CLASSNAME;
        $fetcher = new $class($mockFetcher, $mockCacheManager, $cacheName);

        $tweets = $fetcher->fetch('knplabs', 10, true);

        $this->assertEquals($returnedTweets, $tweets);
    }


}
