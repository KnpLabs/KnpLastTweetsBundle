<?php

namespace Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher;

use Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException;
use Zend\Cache\Manager as CacheManager;

interface LastTweetsFetcherInterface
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
    public function fetch($username, $limit = 10);
}
