<?php

namespace Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher;

use Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException;

interface FetcherCacheableInterface extends FetcherInterface
{
    /**
     * Forces the fetching of the the last tweets of a user on twitter
     *
     * @throws TwitterException     When we do not manage to get a valid answer from the twitter API
     *
     * @param string  $username Name of the user
     * @param integer $limit    Max number of tweets
     *
     * @return array
     */
    public function forceFetch($username, $limit = 10);
}
