<?php

namespace Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher;

use Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException;
use Knp\Bundle\LastTweetsBundle\Twitter\Tweet;
use Buzz\Browser;

class ApiFetcher implements FetcherInterface
{
    protected $browser;
    
    public function __construct(Browser $browser)
    {
        $this->browser = $browser;
    }
    
    public function fetch($usernames, $count = 10, $excludeReplies = true, $includeRts = false, $retryCall = 1)
    {
        if (!is_array($usernames)) {
            $usernames = array((string) $usernames);
        }
        
        if ($count > 200) {
            throw new TwitterException('Maximum limit of tweets is 200.');
        }
        
        $maxId = 0;
        $page = 1;
        $limit = $count;
        $count *= 2; // in order to decrease api requests
        $combineData = array();
        
        while ($page && ($page - 1) <= $retryCall) { // using only if we don't have enough tweets
            foreach ($usernames as $username) { // aggregate tweets for every username
                $queryString = sprintf('?screen_name=%s&count=%d&trim_user=1&exclude_replies=%d&include_rts=%d&page=%d', urlencode($username), $count, $excludeReplies, $includeRts, $page);

                $url = 'http://api.twitter.com/1/statuses/user_timeline.json' . $queryString;
                
                if($maxId) {
                    $url .= sprintf("&max_id=%d", $maxId);
                }
                
                $data = $this->fetchTweets($url);
                
                // we need to inject username, when use "trim_user"
                array_walk($data, function(&$tweet) use($username) {
                    if (is_array($tweet)) {
                        $tweet['username'] = $username;
                    }
                });

                $combineData = array_merge($combineData, $data);
            }
            
            if (count($combineData) < $limit) {
                $maxId = end($combineData);
                $maxId = $maxId['id_str'];
                
                $page++;
            } else {
                usort($combineData, function($a, $b) {
                    return ($a['id_str'] > $b['id_str']) ? -1 : 1;
                });

                $combineData = array_slice($combineData, null, $limit);   
            }
        }
        
        $tweets = array();
        
        foreach ($combineData as $tweetData) {
            $tweets[] = $this->createTweet($tweetData);
        }

        return $tweets;
    }
    
    protected function fetchTweets($url)
    {
        $data = $this->getContents($url);
        
        if (empty($data)) {
            throw new TwitterException('Received empty data from api.twitter.com');
        }

        $data = json_decode($data, true);

        if (null === $data) {
            throw new TwitterException('Unable to decode data from api.twitter.com');
        }
        
        return $data;
    }
    
    protected function getContents($url)
    {
        $response = $this->browser->get($url);
        
        return $response->getContent();
    }

    protected function createTweet($data)
    {
        return new Tweet($data);
    }
}
