<?php

namespace Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher;

use Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException;
use Knp\Bundle\LastTweetsBundle\Twitter\Tweet;

class ApiLastTweetsFetcher implements LastTweetsFetcherInterface
{
    public function fetch($username, $limit = 10)
    {
        $url = sprintf('http://api.twitter.com/1/statuses/user_timeline.json?screen_name=%s', $username);

        $data = $this->getContents($url);

        if (empty($data)) {
            throw new TwitterException('Received empty data from api.twitter.com');
        }

        $data = json_decode($data, true);

        if (null === $data) {
            throw new TwitterException('Unable to decode data from api.twitter.com');
        }

        $i = 0;
        $tweets = array();

        foreach ($data as $tweetData) {
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

    protected function getContents($url)
    {
        return @file_get_contents($url);
    }

    protected function createTweet($data)
    {
        return new Tweet($data);
    }
}
