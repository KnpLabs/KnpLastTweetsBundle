<?php

namespace Knp\Bundle\LastTweetsBundle\Twitter;

class Tweet
{
    protected $id;
    protected $createdAt;
    protected $text;
    protected $isReply;
    protected $username;

    public function __construct(array $object)
    {
        $this->id = $object['id'];
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
        return $this->getText();
    }

    public function getUrl()
    {
        return sprintf('https://twitter.com/%s/status/%s', $this->getUsername(), $this->getId());
    }
}
