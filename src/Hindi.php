<?php namespace MusicCrawler;

use MusicCrawler\MusicServiceHandler;
use Symfony\Component\DomCrawler\Crawler;
use MusicCrawler\Curl;

Class Hindi implements MusicServiceHandler
{
    private $url = "https://mr-johal.com/category.php?cat=Hindi%20Single#gsc.tab=0";

    private $curl;

    private $crawler;

    private $list;

    private $html;

    private $track = [];

    public function __construct()
    {
        $this->curl = new Curl();
        $this->crawler = new Crawler();
    }

    public function getList() {
        $html = preg_replace('/(<!--(.*?)>)/', '',$this->curl->getHtml($this->url));
        $this->crawler->addContent($html);
        $List = $this->crawler->filter('a.touch')->each(function ($node, $i){
            $Url = $node->attr('href');
            if (preg_match('/html/i',$Url)) {
                return array(
                    'url' => $Url,
                    'title' => $node->filter('img')->attr('title')
                );
            }
        });
        return array_values(array_filter($List));
    }

    public function setUrl($url)
    {
        $this->url = $url;
        $html = preg_replace('/<!--(.*?)>/', '',$this->curl->getHtml($this->url));
        $this->crawler->addContent($html);
    }

    public function getResult()
    {
        $this->track['info'] = $this->getInfo();
        $this->track['cover'] = $this->getCover();
        $this->track['downloadLinks'] = $this->getDownloadLinks();
        return $this->track;
    }

    public function getInfo()
    {
        $AlbumInfo = $this->crawler->filter('.albumInfo')->filter('.style18')->each(function($node,$i){
            $text = $node->text();
            if(!preg_match('/(Tweet|Share|Top 20)/',$text)){
                $text = preg_replace('/\s\s\s+/','',$text);
                if(preg_match('/Duration/',$text)) {
                    list($key,$min,$sec) =  explode(':',$text);
                    return [$key=>$min.':'.$sec];
                } else {
                    if(preg_match('/:/',$text)){
                        list($key,$value) =  explode(':',$text);
                        return [$key=>$value];
                    }
                }
            }
        });
        return array_merge(...array_filter($AlbumInfo));
    }

    public function getCover() {
        return $this->crawler->filter('.albumCover > img')->attr('src');
    }

    public function getDownloadLinks()
    {
        $links = $this->crawler->filter('a.touch')->each(function ($node, $i) {
            if(!preg_match('/Home Page|Video/i',$node->text())){
                return array(
                    'title' => $node->text(),
                    'link' => $node->attr('href')
                );
            }
        });
        return array_filter($links);
    }

    public function getJson()
    {
        $this->track['info'] = $this->getInfo();
        $this->track['cover'] = $this->getCover();
        $this->track['downloadLinks'] = $this->getDownloadLinks();
        return json_encode($this->track);
    }
}