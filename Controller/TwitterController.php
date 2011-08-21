<?php

namespace Knp\Bundle\LastTweetsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException;
use Symfony\Component\HttpFoundation\Response;

class TwitterController extends Controller
{
    public function lastTweetsAction($username)
    {
        $twitter = $this->get('knp_last_tweets.last_tweets_fetcher');

        try {
            $tweets = $twitter->fetch('knplabs');
        } catch (TwitterException $e) {
            $tweets = array();
        }
        
        $response = $this->render('KnpLastTweetsBundle:Tweet:lastTweets.html.twig', array(
            'username' => $username . date('H:i:s'),
            'tweets' => $tweets,
        ));
        $response->setSharedMaxAge(60);
        
        return $response;
    }
}
