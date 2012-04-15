<?php

namespace Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher;

use Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException;
use Knp\Bundle\LastTweetsBundle\Twitter\Tweet;

use Buzz\Browser;
use Buzz\Browser\Message\Response;

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
        
        $i = 0;
        $maxId = 0;
        $page = 1;
        $limit = $count;
        $count *= 2; // in order to decrease api requests
        $combineData = array();
        
        while ($i < $page && ($page - 1) <= $retryCall) { // using only if we don't have enough tweets
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
            }
            
            $i++;
        }
        
        usort($combineData, function($a, $b) {
            return ($a['id_str'] > $b['id_str']) ? -1 : 1;
        });
        
        $i = 0;
        $tweets = array();
        
        foreach ($combineData as $tweetData) {
            $tweets[] = $this->createTweet($tweetData);

            ++$i;
            if ($i >= $limit) {
                break;
            }
        }
        
        unset($combineData);

        return $tweets;
    }
    
    protected function fetchTweets($url)
    {
        $response = $this->getResponse($url);
        $statusCode = $response->getStatusCode();
                
        $data = $response->getContent();
        
        if (empty($data)) {
            throw new TwitterException('Received empty data from api.twitter.com');
        }
        
        $data = json_decode($data, true);
        
        if (null === $data) {
            throw new TwitterException('Unable to decode data: ' . json_last_error());
        }
        
        if ($statusCode != 200) {
            if (isset($data['error'])) {
                throw new TwitterException(sprintf('Twitter error: %s', $data['error']));
            } else {
                throw new TwitterException(sprintf('Received %d http code.', $statusCode));
            }
        }
                
        return $data;
    }
    
    protected function getResponse($url)
    {        
        return $this->browser->get($url);
    }

    protected function createTweet($data)
    {
        return new Tweet($data);
    }
}
