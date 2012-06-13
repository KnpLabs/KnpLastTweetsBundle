<?php

namespace Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher;

use Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException;
use Knp\Bundle\LastTweetsBundle\Twitter\Tweet;

class OAuthFetcher extends ApiFetcher
{
    private $oauth;

    public function __construct($oauth)
    {
        $this->oauth = $oauth;
    }

    protected function fetchTweets($parameters)
    {
        $data = $this->getResponse('statuses/user_timeline', $parameters);
        $data = @json_decode(@json_encode($data), true);

        if (!is_array($data)) {
            throw new TwitterException('Received wrong data.');
        }
        if (null === $data) {
            throw new TwitterException('Unable to decode data: ' . json_last_error());
        }
        if ($data === false) {
            throw new TwitterException('Received empty data from api.twitter.com');
        }
        if (isset($data['error'])) {
            throw new TwitterException(sprintf('Twitter error: %s', $data['error']));
        }

        return $data;
    }

    protected function getResponse($api, $parameters)
    {
        return $this->oauth->getApi()->get($api, $parameters);
    }
}
