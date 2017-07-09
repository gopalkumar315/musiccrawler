<?php namespace Music;

interface MusicServiceHandler {
    public function getList();

    public function setUrl($url);
}