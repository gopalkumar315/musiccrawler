<?php namespace MusicCrawler;

use MusicCrawler\MusicServiceHandler;

class Youtube implements MusicServiceHandler
{
    private $url = 'https://www.googleapis.com/youtube/v3/activities';

    private $key = 'enter key';

    private $ChannelId;


    public function getList()
    {

    }

    public function getResult()
    {
        $Param = ['part'=>'snippet,contentDetails','maxResults'=>'50','order'=>'date'];
        $Url = $this->url.'?'.http_build_query($Param).'&channelId='.$this->ChannelId.'&key='.$this->key;
        $Response = file_get_contents($Url);
        $Videos = json_decode($Response, true);
        $list = [];
        foreach ($Videos['items'] as $items) {

            try{
                if(! isset($items['contentDetails']['upload'])) {
                    continue;
                } else {
                    $VideoId = $items['contentDetails']['upload']['videoId'];
                }

                $ImageKey = array_key_exists('maxres',$items['snippet']['thumbnails'])?'maxres':'standard';
                $list[] = array(
                    'date'=> date("d-M-Y h:i A", strtotime($items['snippet']['publishedAt'])),
                    'description'=>  $items['snippet']['description'],
                    'title'=> $items['snippet']['title'],
                    'image'=> $items['snippet']['thumbnails'][$ImageKey]['url'],
                    'channel_id'=>$this->ChannelId,
                    'video_id'=> $VideoId
                );

            }catch(Exception $e) {
                echo $e->getMessage();
            }
        }

        return $list;
    }

    public function setUrl($url)
    {

    }

    public function setChannelId($id)
    {
        $this->ChannelId = $id;
    }
}