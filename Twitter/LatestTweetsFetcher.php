<?php

namespace Knp\Bundle\LastTweetsBundle\Twitter;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException;

class LatestTweetsFetcher
{
    /**
     * Fetch the latest tweets of a user on twitter
     *
     * @throws TwitterException     When we do not manage to get a valid answer from the twitter API
     *
     * @param string Name of the user
     * @param int Max number of tweets
     * @return array
     */
    public function fetch($username, $limit = 10)
    {
        $url = sprintf('http://api.twitter.com/1/statuses/user_timeline.json?screen_name=%s', $username);

        $data = $this->getContents($url);

        if (empty($data)) {
            throw new TwitterException('Received empty data from api.twitter.com');
        }

        $data = json_decode($data);

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
        try {
            $data = file_get_contents($url);
        } catch (\Exception $e) {
            $data = false;
        }

        return false;
    }

    protected function createTweet($data)
    {
        return new Tweet($data);
    }
}
