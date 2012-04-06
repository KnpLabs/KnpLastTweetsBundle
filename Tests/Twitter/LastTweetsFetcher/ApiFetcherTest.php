<?php

namespace Knp\Bundle\LastTweetsBundle\Tests\Twitter\LastTweetsFetcher;

use Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher\ApiFetcher;
use Knp\Bundle\LastTweetsBundle\Twitter\Tweet;

class ApiFetcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldLimitFetchedTweets()
    {
        $fixture = json_encode(array(
            'one' => array('id' => 1, 'id_str' => 1, 'text' => 'asdasdasd', 'created_at' => 'Thu Apr 04 11:58:04 +0000 2012'), 
            'two' => array('id' => 2, 'id_str' => 2, 'text' => 'asdasdasd2', 'created_at' => 'Thu Apr 05 12:45:43 +0000 2012'),
            'three' => array('id' => 3, 'id_str' => 3, 'text' => 'asdasdasd3', 'created_at' => 'Thu Apr 06 13:22:28 +0000 2012'),
            'four' => array('id' => 4, 'id_str' => 4, 'text' => 'asdasdasd4', 'created_at' => 'Thu Apr 07 14:36:01 +0000 2012')
        ));

        $browserMock = $this->getMockedBrowser($fixture);

        $fetcher = new ApiFetcher($browserMock);
        $tweets = $fetcher->fetch('knplabs', 3);

        $this->assertCount(3, $tweets);
    }

    /**
     * @test
     */
    public function shouldFetchFromManyAccounts()
    {
        $fixtureKnplabs = json_encode(array(
            'one' => array('id' => 1, 'id_str' => 1, 'text' => 'asdasdasd', 'created_at' => 'Thu Apr 04 11:58:04 +0000 2012'), 
            'two' => array('id' => 2, 'id_str' => 2, 'text' => 'asdasdasd2', 'created_at' => 'Thu Apr 05 12:45:43 +0000 2012')
        ));
        $fixtureKnplabsRu = json_encode(array(
            'three' => array('id' => 3, 'id_str' => 3, 'text' => 'asdasdasd3', 'created_at' => 'Thu Apr 06 13:22:28 +0000 2012'),
            'four' => array('id' => 4, 'id_str' => 4, 'text' => 'asdasdasd4', 'created_at' => 'Thu Apr 07 14:36:01 +0000 2012')
        ));

        $browserMock = $this->getMock('Buzz\Browser');
        $browserMock->expects($this->at(0))
            ->method('get')
            ->with($this->stringContains('knplabs'))
            ->will($this->returnValue($this->getMockedResponse($fixtureKnplabs)));
        $browserMock->expects($this->at(1))
            ->method('get')
            ->with($this->stringContains('knplabsru'))
            ->will($this->returnValue($this->getMockedResponse($fixtureKnplabsRu)));

        $fetcher = new ApiFetcher($browserMock);
        $tweets = $fetcher->fetch(array('knplabs', 'knplabsru'), 4);

        $this->assertCount(4, $tweets);
    }

    /**
     * @test
     */
    public function shouldFetchTweets()
    {
        $fixture = json_encode(array(
            'one' => array('id' => 1, 'id_str' => 1, 'text' => 'tweet1', 'created_at' => 'Thu Apr 04 11:58:04 +0000 2012'), 
            'two' => array('id' => 2, 'id_str' => 2, 'text' => 'tweet2', 'created_at' => 'Thu Apr 05 12:45:43 +0000 2012'),
            'three' => array('id' => 3, 'id_str' => 3, 'text' => 'tweet3', 'created_at' => 'Thu Apr 06 13:22:28 +0000 2012'),
            'four' => array('id' => 4, 'id_str' => 4, 'text' => 'tweet4', 'created_at' => 'Thu Apr 07 14:36:01 +0000 2012')
        ));

        $browserMock = $this->getMockedBrowser($fixture);

        $fetcher = new ApiFetcher($browserMock);
        $tweets = $fetcher->fetch('knplabs', 2);

        $this->assertCount(2, $tweets);
        $this->assertEquals('tweet4', $tweets[0]->getText());
        $this->assertEquals('tweet3', $tweets[1]->getText());
        
        $this->assertInstanceOf('Knp\Bundle\LastTweetsBundle\Twitter\Tweet', $tweets[0]);
        $this->assertInstanceOf('Knp\Bundle\LastTweetsBundle\Twitter\Tweet', $tweets[1]);
    }

    /**
     * @test
     * @expectedException Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException
     */
    public function shouldNotFetchTweetsWhenServiceUnavailable()
    {
        $browserMock = $this->getMockedBrowser(null);

        $fetcher = new ApiFetcher($browserMock);
        $tweets = $fetcher->fetch('knplabs', 2);
    }

    /**
     * @test
     * @expectedException Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException
     */
    public function shouldNotFetchTweetsWhenInvalidJsonData()
    {
        $browserMock = $this->getMockedBrowser('a{');

        $fetcher = new ApiFetcher($browserMock);
        $tweets = $fetcher->fetch('knplabs', 2);
    }

    protected function getMockedBrowser($fixture = array())
    {
        $browser = $this->getMock('Buzz\Browser');
        $response = $this->getMockedResponse($fixture);
        $browser->expects($this->any())
            ->method('get')
            ->will($this->returnValue($response));

        return $browser;
    }

    protected function getMockedResponse($fixture)
    {
        $response = $this->getMock('Buzz\Browser\Message\Response', array('getContent'));

        $response->expects($this->atLeastOnce())
            ->method('getContent')
            ->will($this->returnValue($fixture));

        return $response;
    }
}