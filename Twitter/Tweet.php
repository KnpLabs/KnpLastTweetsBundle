<?php

namespace Knp\Bundle\LastTweetsBundle\Twitter;

class Tweet
{
    protected $id;
    protected $createdAt;
    protected $text;
    protected $username;

    public function __construct(array $object)
    {
        $this->id = $object['id'];
        $this->createdAt = new \DateTime($object['created_at']);
        $this->text = $object['text'];
        $this->username = $object['username'];
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
}
