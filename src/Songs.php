<?php namespace MusicCrawler;
use Symfony\Component\DomCrawler\Crawler;
class Songs
{
    protected $handler;

    public function __construct(MusicServiceHandler $handler)
    {
        $this->handler = $handler;
    }

    public function getList()
    {
        return $this->handler->getList();
    }

    public function setUrl($url)
    {
        $this->handler->setUrl($url);
        return $this;
    }

    public function setChannelId($id)
    {
        $this->handler->setChannelId($id);
        return $this;
    }

    public function getResult()
    {
        return $this->handler->getResult();
    }
}