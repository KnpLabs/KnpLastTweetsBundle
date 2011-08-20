<?php

namespace Knp\Bundle\LastTweetsBundle\Twitter;

class Tweet
{
    protected $id;
    protected $createdAt;
    protected $text;
    protected $isReply;
    protected $username;

    public function __construct(\stdClass $object)
    {
        $this->id = $object->id;
        $this->createdAt = new \DateTime($object['created_at']);
        $this->isReply = (bool) $object['in_reply_to_screen_name'];
        $this->text = $object['text'];
        $this->username = $object['user']['name'];
    }

    public function getText()
    {
        return $this->text;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getId()
    {
        return $this->id;
    }

    public function isReply()
    {
        return $this->isReply;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function __toString()
    {
        return $this->getUrlizedText();
    }

    public function getUrlizedText()
    {
        return self::urlize($this->getText());
    }

    static public function urlize($text)
    {
        $text = strip_tags($text);
        $text = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\">\\2</a>", $text);
        $text = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\">\\2</a>", $text);
        $text = preg_replace("/@(\w+)/", "<a href=\"http://twitter.com/\\1\">@\\1</a>", $text);
        $text = preg_replace("/#(\w+)/", "<a href=\"http://twitter.com/search/\\1\">#\\1</a>", $text);

        return $text;
    }

    public function getUrl()
    {
        return sprintf('https://twitter.com/%s/status/%s', $this->getUsername(), $this->getId());
    }
}
