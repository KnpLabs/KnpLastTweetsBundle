<?php

namespace Knp\Bundle\LastTweetsBundle\Tests\Twitter\LastTweetsFetcher;

use Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher\OAuthFetcher;
use Knp\Bundle\LastTweetsBundle\Twitter\Tweet;

class OAuthFetcherTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('Inori\TwitterAppBundle\Services\TwitterApp')) {
            $this->markTestSkipped(
                'The TwitterAppBundle is not installed.'
            );
        }
    }

    /**
     * @test
     */
    public function shouldFetchTweets()
    {
        $fixtures = json_decode(json_encode(array(
            array('id' => 1, 'id_str' => 1, 'text' => 'tweet1', 'created_at' => 'Thu Apr 04 11:58:04 +0000 2012'),
            array('id' => 2, 'id_str' => 2, 'text' => 'tweet2', 'created_at' => 'Thu Apr 05 12:45:43 +0000 2012'),
            array('id' => 3, 'id_str' => 3, 'text' => 'tweet3', 'created_at' => 'Thu Apr 06 13:22:28 +0000 2012'),
            array('id' => 4, 'id_str' => 4, 'text' => 'tweet4', 'created_at' => 'Thu Apr 07 14:36:01 +0000 2012')
        )));

        $mockedOAuth = $this->getMockedOAuth($fixtures);
        $fetcher = new OAuthFetcher($mockedOAuth);

        $tweets = $fetcher->fetch('knplabs', 4);
        $this->assertCount(4, $tweets);
        $this->assertEquals('tweet1', $tweets[3]->getText());
        $this->assertEquals('tweet4', $tweets[0]->getText());
    }

    /**
     * @test
     * @expectedException Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException
     */
    public function shouldNotFetchTweetsWithBadResponse()
    {
        $fixture = 'Hello amigos!';

        $mockedOAuth = $this->getMockedOAuth($fixture);
        $fetcher = new OAuthFetcher($mockedOAuth);
        $fetcher->fetch('knplabs');
    }

    /**
     * @test
     * @expectedException Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException
     */
    public function shouldNotFetchTweetsWithEmptyResponse()
    {
        $fixture = null;

        $mockedOAuth = $this->getMockedOAuth($fixture);
        $fetcher = new OAuthFetcher($mockedOAuth);
        $fetcher->fetch('knplabs');
    }

    /**
     * @test
     * @expectedException Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException
     */
    public function shouldNotFetchTweetsWithErrorResponse()
    {
        $fixture = json_decode(json_encode(array(
            'error' => 'Something wrong happened.'
        )), false);

        $mockedOAuth = $this->getMockedOAuth($fixture);
        $fetcher = new OAuthFetcher($mockedOAuth);
        $fetcher->fetch('knplabs');
    }

    protected function getMockedOAuth($fixture)
    {
        $mockedApi = $this->getMockedApi($fixture);

        $oauth = $this->getMock('Inori\TwitterAppBundle\Services\TwitterApp', array('getApi'), array($mockedApi));
        $oauth->expects($this->any())
            ->method('getApi')
            ->will($this->returnValue($mockedApi));

        return $oauth;
    }

    protected function getMockedApi($fixture)
    {
        $api = $this->getMock('\TwitterOAuth', array('get'));

        $api->expects($this->any())
            ->method('get')
            ->will($this->returnValue($fixture));

        return $api;
    }

}