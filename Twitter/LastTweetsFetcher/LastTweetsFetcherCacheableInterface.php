<?php

namespace Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher;

use Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException;

interface LastTweetsFetcherCacheableInterface extends LastTweetsFetcherInterface
{
    /**
     * Forces the fetching of the the last tweets of a user on twitter
     *
     * @throws TwitterException     When we do not manage to get a valid answer from the twitter API
     *
     * @param string Name of the user
     * @param int Max number of tweets
     * @return array
     */
    public function forceFetch($username, $limit = 10);
}
