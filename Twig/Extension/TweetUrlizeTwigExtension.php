<?php

namespace Knp\Bundle\LastTweetsBundle\Twig\Extension;

use Knp\Bundle\LastTweetsBundle\Helper\TweetUrlizeHelper;

class TweetUrlizeTwigExtension extends \Twig_Extension
{
    private $helper;

    public function __construct(TweetUrlizeHelper $helper)
    {
        $this->helper = $helper;
    }

    public function getFilters()
    {
        return array(
            'knp_tweet_urlize' => new \Twig_SimpleFilter('filterTweet', array($this, 'filterTweet'), array('pre_escape' => 'html', 'is_safe' => array('html'))),
        );
    }

    public function filterTweet($text)
    {
        return TweetUrlizeHelper::urlize($text);
    }

    public function getName()
    {
        return 'knp_tweet_urlize';
    }
}
