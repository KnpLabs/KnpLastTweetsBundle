<?php

namespace Knp\Bundle\LastTweetsBundle\Tests\Twitter\LastTweetsFetcher;

use Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher\ArrayFetcher;
use Knp\Bundle\LastTweetsBundle\Twitter\Tweet;

class ArrayFetcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnTweets()
    {
        $fixtures = array(
            array('id' => 1, 'username' => 'knplabs', 'created_at' => '2011-02-13 21:12:13', 'text' => 'first text'),
            array('id' => 2, 'username' => 'knplabs', 'created_at' => '2011-02-13 21:12:13', 'text' => 'second text')
        );

        $fetcher = new ArrayFetcher($fixtures);
        $tweets = $fetcher->fetch(null);

        $this->assertEquals('first text', $tweets[0]);
        $this->assertEquals('second text', $tweets[1]);
    }

    /**
     * @test
     */
    public function shouldReturnLimitedTweets()
    {
        $fixtures = array(
            array('id' => 1, 'username' => 'knplabs', 'created_at' => '2011-02-13 21:12:13', 'text' => 'first text'),
            array('id' => 2, 'username' => 'knplabs', 'created_at' => '2011-02-13 21:12:13', 'text' => 'second text'),
            array('id' => 3, 'username' => 'knplabs', 'created_at' => '2011-02-13 21:12:13', 'text' => 'third text')
        );

        $fetcher = new ArrayFetcher($fixtures);
        $tweets = $fetcher->fetch(null, 2);

        $this->assertCount(2, $tweets);
    }
}