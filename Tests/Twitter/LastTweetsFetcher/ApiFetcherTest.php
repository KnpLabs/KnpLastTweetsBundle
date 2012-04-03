<?php

namespace Knp\Bundle\LastTweetsBundle\Tests\Twitter\LastTweetsFetcher;

use Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher\ApiFetcher;
use Knp\Bundle\LastTweetsBundle\Twitter\Tweet;

class ApiFetcherTest extends \PHPUnit_Framework_TestCase
{
    const CLASSNAME = 'Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher\ApiFetcher';
    
    public function testMultiAccountFetch()
    {
        $mockedTweet = $this->getMockedTweet();
        
        $fixture = json_encode(array('one', 'two', 'three'));
        
        $fetcher = $this->getMockedFetcher($fixture, 3);
        
        $fetcher->expects($this->exactly(3))
            ->method('createTweet')
            ->will($this->returnValue($mockedTweet));
        
        $tweets = $fetcher->fetch(array('knplabs', 'knplabsru'), 3);
        
        $this->assertEquals(3, count($tweets));   
    }
    
    public function testFetchTweetCreation()
    {
        $fixture = json_encode(array('ipsum'));

        $fetcher = $this->getMockedFetcher($fixture);

        $fetcher->expects($this->any())
            ->method('createTweet')
            ->with($this->equalTo('ipsum'))
            ->will($this->returnValue($this->getMockedTweet()));

        $tweets = $fetcher->fetch('knplabs');
    }

    public function testFetchReturnsTweets()
    {
        // Mock a tweet
        $mockedTweet = $this->getMockedTweet();

        // Mock the fetcher
        $fixture = json_encode(array('one', 'two'));

        $fetcher = $this->getMockedFetcher($fixture);

        $fetcher->expects($this->exactly(2))
            ->method('createTweet')
            ->will($this->returnValue($mockedTweet));

        $tweets = $fetcher->fetch('knplabs');
        
        $this->assertEquals(2, count($tweets));
    }

    public function testFetchReturnsLimit()
    {
        $mockedTweet = $this->getMockedTweet();

        $fixture = json_encode(array('one', 'two', 'three', 'four'));

        $fetcher = $this->getMockedFetcher($fixture, 3);

        $fetcher->expects($this->exactly(3))
            ->method('createTweet')
            ->will($this->returnValue($mockedTweet));

        $tweets = $fetcher->fetch('knplabs', 3);

        $this->assertEquals(3, count($tweets));
    }
    
    /**
     * @expectedException Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException
     */
    public function testUnableToFetchData()
    {
        $fetcher = $this->getMock(
            self::CLASSNAME,
            array('getContents', 'createTweet')
        );

        $fetcher->expects($this->once())
            ->method('getContents')
            ->with($this->equalTo('http://api.twitter.com/1/statuses/user_timeline.json?screen_name=knplabs&count=10&trim_user=1&exclude_replies=true'))
            ->will($this->returnValue(null));

        $tweets = $fetcher->fetch('knplabs');
    }

    /**
     * @expectedException Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException
     */
    public function testFetchBadData()
    {
        $fetcher = $this->getMock(
            self::CLASSNAME,
            array('getContents', 'createTweet')
        );

        $fetcher->expects($this->once())
            ->method('getContents')
            ->with($this->equalTo('http://api.twitter.com/1/statuses/user_timeline.json?screen_name=knplabs&count=10&trim_user=1&exclude_replies=true'))
            ->will($this->returnValue('a{'));

        $tweets = $fetcher->fetch('knplabs');
    }

    protected function getMockedTweet()
    {
        return $this->getMock('Knp\Bundle\LastTweetsBundle\Twitter\Tweet', array(), array(), '', false);
    }

    protected function getMockedFetcher($fixture, $count = 10)
    {
        $fetcher = $this->getMock(
            self::CLASSNAME,
            array('getContents', 'createTweet')
        );

        $fetcher->expects($this->atLeastOnce())
            ->method('getContents')
            ->with($this->logicalOr(
               $this->equalTo(sprintf('http://api.twitter.com/1/statuses/user_timeline.json?screen_name=knplabs&count=%d&trim_user=1&exclude_replies=true', $count)),
               $this->equalTo(sprintf('http://api.twitter.com/1/statuses/user_timeline.json?screen_name=knplabsru&count=%d&trim_user=1&exclude_replies=true', $count))
            ))
            ->will($this->returnValue($fixture));

        return $fetcher;
    }

}
