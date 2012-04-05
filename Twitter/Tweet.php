<?php

namespace Knp\Bundle\LastTweetsBundle\Twitter;

class Tweet
{
    protected $id;
    protected $createdAt;
    protected $text;
    protected $username;
    protected $isReply;
    protected $isRts;

    public function __construct(array $object)
    {
        $this->id = $object['id'];
        $this->createdAt = new \DateTime($object['created_at']);
        $this->text = $object['text'];
        $this->username = $object['username'];
        $this->isReply = isset($object['in_reply_to_screen_name']);
        $this->isRts = isset($object['retweeted_status']);
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

    public function getUsername()
    {
        return $this->username;
    }

    public function __toString()
    {
        return $this->getText();
    }

    public function getUrl()
    {
        return sprintf('https://twitter.com/%s/status/%s', $this->getUsername(), $this->getId());
    }
    
    public function isReply()
    {
        return $this->isReply;
    }
    
    public function isRts()
    {
        return $this->isRts;
    }
}
