<?php

namespace Knp\Bundle\LastTweetsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException;
use Symfony\Component\HttpFoundation\Response;

class TwitterController extends Controller
{
    public function lastTweetsAction($username, $limit = 10, $age = null)
    {
        $twitter = $this->get('knp_last_tweets.last_tweets_fetcher');

        try {
            $tweets = $twitter->fetch($username, $limit);
        } catch (TwitterException $e) {
            $tweets = array();
        }
        
        $response = $this->render('KnpLastTweetsBundle:Tweet:lastTweets.html.twig', array(
            'username' => $username,
            'tweets' => $tweets,
        ));

        if ($age) {
            $response->setSharedMaxAge($age);
        }
        
        return $response;
    }
}
