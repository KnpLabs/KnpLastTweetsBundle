<?php

namespace Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher;

use Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException;
use Doctrine\Common\Cache\Cache;

class DoctrineCacheFetcher implements FetcherCacheableInterface
{
    protected $cacheManager;
    protected $fetcher;

    public function __construct(FetcherInterface $fetcher, Cache $cacheManager)
    {
        $this->fetcher = $fetcher;
        $this->cacheManager = $cacheManager;
    }
    
    public function fetch($username, $limit = 10, $forceRefresh = false)
    {
        if (!is_array($username)) {
            $username = array((string) $username);
        }
        
        $cacheId = 'knp_last_tweets_' . implode('_', $username) . '_' . $limit;

        $tweets = $this->cacheManager->fetch($cacheId);
        
        if ($forceRefresh || false === $tweets ) {
            $tweets = $this->fetcher->fetch($username, $limit);

            $this->cacheManager->save($cacheId, $tweets);
        }
        
        return $tweets;
    }
    
    public function forceFetch($username, $limit = 10)
    {
        return $this->fetch($username, $limit);
    }
}
