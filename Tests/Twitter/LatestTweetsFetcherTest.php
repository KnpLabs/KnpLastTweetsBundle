<?php

namespace Knp\Bundle\LatestTweetsBundle\Tests\Twitter;

use Knp\Bundle\LastTweetsBundle\Twitter\LatestTweetsFetcher;
use Knp\Bundle\LastTweetsBundle\Twitter\Tweet;

class LatestTweetsFetcherTest extends \PHPUnit_Framework_TestCase
{

    public function testFetchTweetCreation()
    {
        $fixture = json_encode(array('lorem'));
        
        $fetcher = $this->getMockedFetcher($fixture);

        $fetcher->expects($this->any())
            ->method('createTweet')
            ->with($this->equalTo('lorem'))
            ->will($this->returnValue($this->getMockedTweet()));

        $tweets = $fetcher->fetch('knplabs');
    }

    public function testFetchReturnsTweets()
    {
        // Mock a tweet
        $mockedTweet = $this->getMockedTweet(false);
        
        // Mock the fetcher
        $fixture = json_encode(array('one', 'two'));

        $fetcher = $this->getMockedFetcher($fixture);
        
        $fetcher->expects($this->exactly(2))
            ->method('createTweet')
            ->will($this->returnValue($mockedTweet));

        $tweets = $fetcher->fetch('knplabs');
        
        // Test
        $this->assertEquals(2, count($tweets));
    }

    public function testFetchOnlyIfNotAReply()
    {
        // Mock a tweet
        $mockedTweet = $this->getMockedTweet(true);

        // Mock the fetcher
        $fixture = json_encode(array('one', 'two'));

        $fetcher = $this->getMockedFetcher($fixture);

        $fetcher->expects($this->exactly(2))
            ->method('createTweet')
            ->will($this->returnValue($mockedTweet));

        $tweets = $fetcher->fetch('knplabs');

        // Test
        $this->assertEquals(0, count($tweets));
    }

    public function testFetchReturnsLimit()
    {
        $mockedTweet = $this->getMockedTweet(false);
        
        $fixture = json_encode(array('one', 'two', 'three', 'four'));

        $fetcher = $this->getMockedFetcher($fixture);
        
        $fetcher->expects($this->exactly(3))
            ->method('createTweet')
            ->will($this->returnValue($mockedTweet));

        $tweets = $fetcher->fetch('knplabs', 3);
        
        $this->assertEquals(3, count($tweets));
    }

    protected function getMockedTweet($getIsReply = null)
    {
        $methods = (null == $getIsReply) ? array() : array('getIsReply');
        
        $tweet = $this->getMock('Knp\Bundle\LastTweetsBundle\Twitter\Tweet', $methods, array(), '', false);
        
        if (null !== $tweet) {
            $tweet->expects($this->any())
                ->method('getIsReply')
                ->will($this->returnValue($getIsReply))
            ;
        }
        
        return $tweet;
    }

    protected function getMockedFetcher($fixture)
    {
        $fetcher = new LatestTweetsFetcher();
        $fetcher = $this->getMock('Knp\Bundle\LastTweetsBundle\Twitter\LatestTweetsFetcher', array('getContents', 'createTweet'));

        $fetcher->expects($this->once())
            ->method('getContents')
            ->with($this->equalTo('http://api.twitter.com/1/statuses/user_timeline.json?screen_name=knplabs'))
            ->will($this->returnValue($fixture));

        return $fetcher;
    }

}
