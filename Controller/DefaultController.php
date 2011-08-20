<?php

namespace Knp\Bundle\LastTweetsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    
    public function latestAction($username)
    {
        $twitter = $this->get('knp_last_tweets.latest_tweets_fetcher');
        
        try {
            $tweets = $twitter->fetch('knplabs');
        } catch (\Exception $e) {
            $tweets = array();
        }

        return $this->render('KnpLastTweetsBundle:Default:latest.html.twig', array(
            'username' => $username,
            'tweets' => $tweets,
        ));
    }
}
