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

                $parameters = array(
                    'screen_name' => urlencode($username),
                    'count' => $count,
                    'trim_user' => 1,
                    'exclude_replies' => (int) $excludeReplies,
                    'include_rts' => (int) $includeRts,
                    'page' => $page
                );

                if($maxId) {
                    $parameters['max_id'] = $maxId;
                }
                
                $data = $this->fetchTweets($parameters);
                
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
    
    protected function fetchTweets($parameters)
    {
        $response = $this->getResponse('statuses/user_timeline', $parameters);
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
    
    protected function getResponse($api, $parameters)
    {
        return $this->browser->get(sprintf("http://api.twitter.com/1/%s.json?%s", $api, implode("&", $this->buildQuery($parameters))));
    }

    protected function createTweet($data)
    {
        return new Tweet($data);
    }

    private function buildQuery($parameters)
    {
        if (!is_array($parameters)) {
            return array();
        } else {
            array_walk($parameters, function($value, $key) use(&$parameters) {
                 $parameters[$key] = $key . '=' . $value;
            });

            return $parameters;
        }
    }
}
