<?php namespace Music;

use MusicCrawler\MusicServiceHandler;
use Symfony\Component\DomCrawler\Crawler;
use MusicCrawler\Curl;

/**
 * Class Djjohal
 * @package Music
 */
class Genius implements MusicServiceHandler
{
    private $url = "https://genius.com/api/songs/chart?page=1&per_page=30&time_period=day";

    public function __construct()
    {
        $this->curl = new Curl();
        $this->crawler = new Crawler();
    }

    public function getList()
    {
        $Content = file_get_contents($this->url);
        $List = json_decode($Content);
        $List = $List->response->chart_items;
        $items = array();
        foreach ($List as $row) {
            $items[] = array (
                'title'=>$row->item->full_title,
                'image'=>$row->item->song_art_image_url,
                'link'=> "https://genius.com/api".$row->item->api_path,
            );
        }
        return $items;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getResult()
    {
        $Content = file_get_contents($this->url);
        $SongDetail = json_decode($Content);

        $EmbedCode = '';
        if ($SongDetail->response->song->youtube_url != '') {
            $url = $SongDetail->response->song->youtube_url;
            parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
            $YoutubeCode = $my_array_of_vars['v'];
            $EmbedCode = "https://youtube.com/embed/$YoutubeCode";
        }
        return array(
            'video' => $EmbedCode,
            'soundcloud'=>$SongDetail->response->song->soundcloud_url,
            'youtube' =>$SongDetail->response->song->youtube_url
        );
    }
}