<?php

namespace Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher;

use Knp\Bundle\LastTweetsBundle\Twitter\Tweet;

class ArrayFetcher implements FetcherInterface
{
    private $data;

    public function __construct(array $data = array())
    {
        $this->data = $data;
    }

    /**
     * {@inheritDoc}
     */
    public function fetch($username, $limit = 10)
    {
        $tweets = array();

        $i = 0;
        foreach ($this->data as $tweetData) {
            $tweets[] = $this->createTweet($tweetData);

            $i++;
            if ($i >= $limit) {
                break;
            }
        }

        return $tweets;
    }

    protected function createTweet($data)
    {
        return new Tweet($data);
    }
}
