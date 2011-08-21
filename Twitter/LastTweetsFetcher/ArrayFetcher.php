<?php

namespace Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher;

use Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException;
use Knp\Bundle\LastTweetsBundle\Twitter\Tweet;

class ArrayFetcher implements FetcherInterface
{
    protected $data;
    
    public function __construct(array $data = array())
    {
        $this->data = $data;
    }
    
    /**
     * Fetch the last tweets of a user on twitter
     *
     * @throws TwitterException     When we do not manage to get a valid answer from the twitter API
     *
     * @param string Name of the user
     * @param int Max number of tweets
     * @return array
     */
    public function fetch($username, $limit = 10)
    {
        $tweets = array();

        $i = 0;
        foreach ($this->data as $tweetData) {
            $tweet = $this->createTweet($tweetData);
            if (!$tweet->isReply()) {
                $tweets[] = $tweet;

                $i++;
                if ($i >= $limit) {
                    break;
                }
            }
        }

        return $tweets;
    }

    protected function createTweet($data)
    {
        return new Tweet($data);
    }
}
