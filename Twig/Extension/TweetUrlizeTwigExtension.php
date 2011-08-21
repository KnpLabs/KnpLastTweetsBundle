<?php

namespace Knp\Bundle\LastTweetsBundle\Twig\Extension;

class TweetUrlizeTwigExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'knp_tweet_urlize' => new \Twig_Filter_Method($this, 'filterTweet', array('pre_escape' => 'html', 'is_safe' => array('html'))),
        );
    }

    public function filterTweet($text)
    {
        return self::urlize($text);
    }

    /**
     * Replace urls, #hastags and @mentions by their urls
     * Should be moved to a Renderer class in order for it to be used by 
     * something else than twig
     * 
     * @param string A tweet
     * @return the urlized tweed
     */
    static public function urlize($text)
    {
        $text = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\">\\2</a>", $text);
        $text = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\">\\2</a>", $text);
        $text = preg_replace("/@(\w+)/", "<a href=\"http://twitter.com/\\1\">@\\1</a>", $text);
        $text = preg_replace("/#(\w+)/", "<a href=\"http://twitter.com/search/\\1\">#\\1</a>", $text);

        return $text;
    }


    public function getName()
    {
        return 'knp_tweet_urlize';
    }
}
