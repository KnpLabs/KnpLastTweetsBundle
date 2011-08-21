<?php

namespace Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher;

use Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException;
use Zend\Cache\Manager as CacheManager;

class ZendCacheFetcher implements FetcherCacheableInterface
{
    protected $cacheManager;
    protected $cacheName;
    protected $fetcher;

    public function __construct(FetcherInterface $fetcher, CacheManager $cacheManager, $cacheName)
    {
        $this->fetcher = $fetcher;
        $this->cacheManager = $cacheManager;
        $this->cacheName = $cacheName;
    }
    
    public function fetch($username, $limit = 10, $forceRefresh = false)
    {
        $cache = $this->cacheManager->getCache($this->cacheName);
        if (null === $cache) {
            throw new \Exception("Unknown Zend Cache '".$this->cacheName."'");
        }
        $cacheId = 'knp_last_tweets_'.$username.'_'.$limit;
        
        if ($forceRefresh || false === ($tweets = $cache->load($cacheId))) {
            $tweets = $this->fetcher->fetch($username, $limit);

            $cache->save($tweets, $cacheId);
        }
        
        return $tweets;
    }
    
    public function forceFetch($username, $limit = 10)
    {
        return $this->fetch($username, $limit, true);
    }
}
