<?php

namespace Knp\Bundle\LatestTweetsBundle\Tests\Twitter;

use Knp\Bundle\LastTweetsBundle\Twitter\LatestTweetsFetcher;
use Knp\Bundle\LastTweetsBundle\Twitter\Tweet;

class TweetTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider getUrlizedData
     */
    public function testUrlize($expected, $text)
    {
        $this->assertEquals($expected, Tweet::urlize($text));
    }

    public function getUrlizedData()
    {
        return array(
            array('I am <a href="http://twitter.com/mbontemps">@mbontemps</a>. What about you?', 'I am @mbontemps. What about you?'),
            array('I am <a href="http://twitter.com/search/symfony">#symfony</a>. What about you?', 'I am #symfony. What about you?'),
            array('I am <a href="http://www.knplabs.com/en">http://www.knplabs.com/en</a> - What about you?', 'I am http://www.knplabs.com/en - What about you?'),
            array('I am <a href="http://www.knplabs.com/en">www.knplabs.com/en</a> - What about you?', 'I am www.knplabs.com/en - What about you?'),
        );
    }
}
