<?php

namespace Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher;

use Knp\Bundle\LastTweetsBundle\CacheProvider\CacheProviderInterface;
use Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException;
use Knp\Bundle\LastTweetsBundle\Twitter\Tweet;

use Buzz\Browser;
use Buzz\Message\Response;

class ApiFetcher implements FetcherInterface
{
    /**
     * @var string
     */
    protected $url = 'http://api.twitter.com/1/%s.json?%s';
    /**
     * @var Browser
     */
    protected $browser;
    /**
     * @var CacheProviderInterface
     */
    protected $cacheProvider;

    /**
     * @var array
     */
    protected $options = array(
        'exclude_replies' => true,
        'include_rts'     => false,

        'retry_call'      => 1,
    );

    /**
     * @param Browser $browser
     */
    public function __construct(Browser $browser)
    {
        $this->browser = $browser;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @throws \InvalidArgumentException
     */
    public function setOption($key, $value)
    {
        if (!array_key_exists($key, $this->options)) {
            throw new \InvalidArgumentException(sprintf('Invalid option used: %s.'));
        }

        $this->options[$key] = $value;
    }

    /**
     * @param CacheProviderInterface $cacheProvider
     */
    public function setCache(CacheProviderInterface $cacheProvider)
    {
        $this->cacheProvider = $cacheProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function hasCache()
    {
        return null !== $this->cacheProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function fetch($usernames, $count = 10, $force = false)
    {
        if (!is_array($usernames)) {
            $usernames = array((string) $usernames);
        }

        if ($count > 200) {
            throw new TwitterException('Maximum limit of tweets is 200.');
        }

        if (false === $force && null !== $this->cacheProvider) {
            $tweets = $this->cacheProvider->fetch($usernames);
            if (!empty($tweets)) {
                return $tweets;
            }
        }

        $i = $maxId = 0;
        $page = 1;
        $limit = $count;
        $count *= 2; // in order to decrease api requests
        $combineData = array();

        // using only if we don't have enough tweets
        while ($i < $page && ($page - 1) <= $this->options['retry_call']) {
            // aggregate tweets for every username
            foreach ($usernames as $username) {
                $parameters = array(
                    'screen_name'     => urlencode($username),
                    'count'           => $count,
                    'trim_user'       => 1,
                    'exclude_replies' => (int) $this->options['exclude_replies'],
                    'include_rts'     => (int) $this->options['include_rts'],
                    'page'            => $page
                );

                if ($maxId) {
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

                ++$page;
            }

            ++$i;
        }

        usort($combineData, function($a, $b) {
            return ($a['id_str'] > $b['id_str']) ? -1 : 1;
        });

        $i = 0;
        $tweets = array();

        foreach ($combineData as $tweetData) {
            $tweets[] = $this->createTweet($tweetData);

            if (++$i >= $limit) {
                break;
            }
        }

        unset($combineData);

        if (null !== $this->cacheProvider) {
            $this->cacheProvider->save($usernames, $tweets);
        }

        return $tweets;
    }

    /**
     * @param array $parameters
     *
     * @return mixed
     *
     * @throws TwitterException
     */
    protected function fetchTweets(array $parameters)
    {
        $response = $this->getResponse('statuses/user_timeline', $parameters);

        $data = $response->getContent();
        if (empty($data)) {
            throw new TwitterException('Received empty data from api.twitter.com');
        }

        $data = json_decode($data, true);
        if (null === $data) {
            throw new TwitterException('Unable to decode data: ' . json_last_error());
        }

        $statusCode = $response->getStatusCode();
        if ($statusCode != 200) {
            if (isset($data['error'])) {
                throw new TwitterException(sprintf('Twitter error: %s', $data['error']));
            } else {
                throw new TwitterException(sprintf('Received %d http code.', $statusCode));
            }
        }

        return $data;
    }

    /**
     * @param string $api
     * @param array $parameters
     *
     * @return Response
     */
    protected function getResponse($api, array $parameters)
    {
        return $this->browser->get(sprintf($this->url, $api, http_build_query($parameters, '', '&')));
    }

    /**
     * @param array $data
     *
     * @return Tweet
     */
    protected function createTweet(array $data)
    {
        return new Tweet($data);
    }
}
