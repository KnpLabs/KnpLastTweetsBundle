<?php

namespace Knp\Bundle\LastTweetsBundle\Tests\Twitter\LastTweetsFetcher;

use Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher\ApiLastTweetsFetcher;
use Knp\Bundle\LastTweetsBundle\Twitter\Tweet;

class ApiLastTweetsFetcherTest extends \PHPUnit_Framework_TestCase
{
    const CLASSNAME = 'Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher\ApiLastTweetsFetcher';

    public function testFetchTweetCreation()
    {
        $fixture = json_encode(array('lorem'));

        $fetcher = $this->getMockedFetcher($fixture);

        $fetcher->expects($this->any())
            ->method('createTweet')
            ->with($this->equalTo('lorem'))
            ->will($this->returnValue($this->getMockedTweet(false)));

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
            ->with($this->equalTo('http://api.twitter.com/1/statuses/user_timeline.json?screen_name=knplabs'))
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
            ->with($this->equalTo('http://api.twitter.com/1/statuses/user_timeline.json?screen_name=knplabs'))
            ->will($this->returnValue('a{'));

        $tweets = $fetcher->fetch('knplabs');
    }

    protected function getMockedTweet($isReplyValue)
    {
        $tweet = $this->getMock('Knp\Bundle\LastTweetsBundle\Twitter\Tweet', array('isReply'), array(), '', false);

        $tweet->expects($this->any())
            ->method('isReply')
            ->will($this->returnValue($isReplyValue));

        return $tweet;
    }

    protected function getMockedFetcher($fixture)
    {
        $fetcher = $this->getMock(
            self::CLASSNAME,
            array('getContents', 'createTweet')
        );

        $fetcher->expects($this->once())
            ->method('getContents')
            ->with($this->equalTo('http://api.twitter.com/1/statuses/user_timeline.json?screen_name=knplabs'))
            ->will($this->returnValue($fixture));

        return $fetcher;
    }

}
