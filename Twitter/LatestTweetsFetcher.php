<?php

namespace Knp\Bundle\LastTweetsBundle\Twitter;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class LatestTweetsFetcher
{

    public function fetch($username, $limit = 10)
    {
        $url = sprintf('http://api.twitter.com/1/statuses/user_timeline.json?screen_name=%s', $username);
        
        try {
            $data = $this->getContents($url);
        } catch(\ErrorException $e) {
            throw new \Exception("Unable to fetch url");
        }
        
        if (0 === strlen($data)) {
            throw new \Exception("No data");
        }
        
        $data = json_decode($data);
        $i = 0;
        $tweets = array();
        
        foreach ($data as &$tweetData) {
            $tweet = $this->createTweet($tweetData);
            if (!$tweet->getIsReply()) {
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
        return file_get_contents($url);
    }

    protected function createTweet($data)
    {
        return new Tweet($data);
    }
}
