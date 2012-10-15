<?php

namespace Knp\Bundle\LastTweetsBundle\Helper;

use Symfony\Component\Templating\Helper\HelperInterface;

class TweetUrlizeHelper implements HelperInterface
{
    private $charset = 'UTF-8';

    /**
     * Replace urls, #hastags and @mentions by their urls
     *
     * @param string $text A tweet message
     *
     * @return string      The "urlized" tweet
     */
    public static function urlize($text)
    {
        $text = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\">\\2</a>", $text);
        $text = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\">\\2</a>", $text);
        $text = preg_replace("/@(\w+)/", "<a href=\"http://twitter.com/\\1\">@\\1</a>", $text);
        $text = preg_replace("/([^&]|^)#(\w+)/", "\\1<a href=\"http://twitter.com/search/\\2\">#\\2</a>", $text);

        return $text;
    }

    /**
     * Sets the default charset.
     *
     * @param string $charset The charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * Gets the default charset.
     *
     * @return string The default charset
     */
    public function getCharset()
    {
        return $this->charset;
    }

    public function getName()
    {
        return 'tweet_urlize';
    }
}
