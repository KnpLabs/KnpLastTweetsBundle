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
        
        $fixture = json_encode(array(
            'one' => array('id_str' => 1, 'text' => 'asdasdasd'), 
            'two' => array('id_str' => 2, 'text' => 'asdasdasd2'), 
            'three' => array('id_str' => 3, 'text' => 'asdasdasd3')
        ));
        
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
        $fixture = json_encode(array(
            'one' => array('id_str' => 1, 'text' => 'asdasdasd'), 
            'two' => array('id_str' => 2, 'text' => 'asdasdasd2')
        ));

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

        $fixture = json_encode(array(
            'one' => array('id_str' => 1, 'text' => 'asdasdasd'), 
            'two' => array('id_str' => 2, 'text' => 'asdasdasd2'), 
            'three' => array('id_str' => 3, 'text' => 'asdasdasd3'),
            'four' => array('id_str' => 4, 'text' => 'asdasdasd4')
        ));

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
            array('getContents', 'createTweet'),
            array($this->getMockedBrowser())
        );

        $fetcher->expects($this->once())
            ->method('getContents')
            ->with($this->stringContains('http://api.twitter.com/1/statuses/user_timeline.json'))
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
            array('getContents', 'createTweet'),
            array($this->getMockedBrowser())
        );

        $fetcher->expects($this->once())
            ->method('getContents')
            ->with($this->stringContains('http://api.twitter.com/1/statuses/user_timeline.json'))
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
            array('getContents', 'createTweet'),
            array($this->getMockedBrowser())
        );

        $fetcher->expects($this->atLeastOnce())
            ->method('getContents')
            ->with($this->stringContains('http://api.twitter.com/1/statuses/user_timeline.json'))
            ->will($this->returnValue($fixture));

        return $fetcher;
    }
    
    protected function getMockedBrowser()
    {
        $browser = $this->getMock('Buzz\Browser');
        
        return $browser;
    }
}
