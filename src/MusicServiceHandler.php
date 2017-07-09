<?php namespace MusicCrawler;

interface MusicServiceHandler {
    public function getList();

    public function setUrl($url);
}