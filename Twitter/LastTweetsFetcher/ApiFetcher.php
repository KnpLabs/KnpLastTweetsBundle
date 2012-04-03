<?php

namespace Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher;

use Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException;
use Knp\Bundle\LastTweetsBundle\Twitter\Tweet;

class ApiFetcher implements FetcherInterface
{
    protected $browser;
    
    public function __construct()
    {
        $this->browser = new \Buzz\Browser;
    }
    
    public function fetch($usernames, $limit = 10)
    {
        if(!is_array($usernames)) {
            $usernames = array((string) $usernames);
        }
        
        $combineData = array();
        
        foreach ($usernames as $username) {
            $url = sprintf('http://api.twitter.com/1/statuses/user_timeline.json?screen_name=%s&count=%d&trim_user=1&exclude_replies=true', $username, $limit);
            $data = $this->getContents($url);
            
            if (empty($data)) {
                throw new TwitterException('Received empty data from api.twitter.com');
            }
            
            $data = json_decode($data, true);
            
            if (null === $data) {
                throw new TwitterException('Unable to decode data from api.twitter.com');
            }
            
            // we need to inject username, when use "trim_user"
            array_walk($data, function(&$tweet) use($username) {
                if (is_array($tweet)) {
                    $tweet['username'] = $username;
                }
            });
            
            $combineData = array_merge($combineData, $data);
        }
        
        usort($combineData, function($a, $b) {
            return ($a['id'] > $b['id']) ? -1 : 1;
        });

        $combineData = array_slice($combineData, null, $limit);
        
        $i = 0;
        $tweets = array();
        
        foreach ($combineData as $tweetData) {
            $tweets[] = $this->createTweet($tweetData);
        }

        return $tweets;
    }

    protected function getContents($url)
    {
        return $this->browser->get($url)->getContent();
    }

    protected function createTweet($data)
    {
        return new Tweet($data);
    }
}
