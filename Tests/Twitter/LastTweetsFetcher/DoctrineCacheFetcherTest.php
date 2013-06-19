<?php

namespace Knp\Bundle\LastTweetsBundle\Tests\Twitter\LastTweetsFetcher;

use Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher\ApiFetcher;
use Knp\Bundle\LastTweetsBundle\Twitter\Tweet;

class DoctrineCacheFetcherTest extends \PHPUnit_Framework_TestCase
{
    const CLASSNAME = 'Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher\DoctrineCacheFetcher';

    public function testFetchCachedWithArrayUsername()
    {
        $mockFetcher = $this->getMock('Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher\FetcherInterface');
        $mockCacheManager = $this->getMock('Doctrine\Common\Cache\Cache');
        $returnedTweets = array('ipsum');

        $mockCacheManager->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo('knp_last_tweets_knplabs_knplabsru_10'))
            ->will($this->returnValue($returnedTweets));

        $class = self::CLASSNAME;
        $fetcher = new $class($mockFetcher, $mockCacheManager);

        $tweets = $fetcher->fetch(array('knplabs', 'knplabsru'));

        $this->assertEquals($returnedTweets, $tweets);
    }
    
    public function testFetchCached()
    {
        $mockFetcher = $this->getMock('Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher\FetcherInterface');
        $mockCacheManager = $this->getMock('Doctrine\Common\Cache\Cache');
        $returnedTweets = array('ipsum');

        $mockCacheManager->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo('knp_last_tweets_knplabs_10'))
            ->will($this->returnValue($returnedTweets));

        $class = self::CLASSNAME;
        $fetcher = new $class($mockFetcher, $mockCacheManager);

        $tweets = $fetcher->fetch('knplabs');

        $this->assertEquals($returnedTweets, $tweets);
    }

    public function testFetchNotCached()
    {
        $mockFetcher = $this->getMock('Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher\FetcherInterface');
        $mockCacheManager = $this->getMock('Doctrine\Common\Cache\Cache');
        $cacheId = 'knp_last_tweets_knplabs_10';
        $returnedTweets = array('ipsum');

        $mockFetcher->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo(array('knplabs')), $this->equalTo(10))
            ->will($this->returnValue($returnedTweets));

        $mockCacheManager->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo($cacheId))
            ->will($this->returnValue(false));

        $mockCacheManager->expects($this->once())
            ->method('save')
            ->with($this->equalTo($cacheId), $this->equalTo($returnedTweets));

        $class = self::CLASSNAME;
        $fetcher = new $class($mockFetcher, $mockCacheManager, $returnedTweets);

        $tweets = $fetcher->fetch('knplabs');

        $this->assertEquals($returnedTweets, $tweets);
    }
    
    public function testFetchForceRefresh()
    {
        $mockFetcher = $this->getMock('Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher\FetcherInterface');
        $mockCacheManager = $this->getMock('Doctrine\Common\Cache\Cache');
        $returnedTweets = array('ipsum');

        $mockFetcher->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo(array('knplabs')), $this->equalTo(10))
            ->will($this->returnValue($returnedTweets));

        $mockCacheManager->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo('knp_last_tweets_knplabs_10'))
            ->will($this->returnValue($returnedTweets));

        $class = self::CLASSNAME;
        $fetcher = new $class($mockFetcher, $mockCacheManager);

        $tweets = $fetcher->fetch('knplabs', 10, true);

        $this->assertEquals($returnedTweets, $tweets);
    }


}
