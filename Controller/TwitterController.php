<?php

namespace Knp\Bundle\LastTweetsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException;

class TwitterController extends Controller
{
    public function latestAction($username)
    {
        $twitter = $this->get('knp_last_tweets.latest_tweets_fetcher');

        try {
            $tweets = $twitter->fetch('knplabs');
        } catch (TwitterException $e) {
            $tweets = array();
        }

        return $this->render('KnpLastTweetsBundle:Tweet:latest.html.twig', array(
            'username' => $username,
            'tweets' => $tweets,
        ));
    }
}
