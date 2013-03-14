<?php

namespace Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher;

use Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException;

interface FetcherInterface
{
    /**
     * Fetch the last tweets of a user on twitter
     *
     * @param string  $username Name of the user
     * @param integer $limit    Max number of tweets
     * @param boolean $force    Ignore cache if true
     *
     * @return array
     *
     * @throws TwitterException When we do not manage to get a valid answer from the twitter API
     */
    public function fetch($username, $limit = 10, $force = false);

    /**
     * @return boolean
     */
    public function hasCache();
}
