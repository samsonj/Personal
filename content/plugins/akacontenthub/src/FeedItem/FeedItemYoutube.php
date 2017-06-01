<?php

namespace ContentHub\FeedItem;

class FeedItemYoutube extends FeedItem {

    public $api_key;
    
	public function __construct($api_key) {   
    	parent::__construct();
        $this->api_key = $api_key;
    }

    public function getVideo($id) {
        
        $url = 'https://www.googleapis.com/youtube/v3/videos?id='.$id.'&key='.$this->api_key.'&part=snippet';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $rsp = curl_exec($ch);
        curl_close($ch);
        $rsp_obj = json_decode($rsp, true);

        if(empty($rsp_obj['error'])) {

            // if the full image exists
            $image = 'http://img.youtube.com/vi/'.$id.'/maxresdefault.jpg';
            $imageHeaders = get_headers($image);
            if(strpos($imageHeaders[0], '200 OK') == false) {
                $image = str_replace('maxresdefault.jpg', 'hqdefault.jpg', $image);
            }
            $response = array('image' => $image, 'title' => $rsp_obj['items'][0]['snippet']['title'], 'id' => $id, 'channel' => $rsp_obj['items'][0]['snippet']['channelTitle'], 'date' => $rsp_obj['items'][0]['snippet']['publishedAt']);
            return $response;

        } else {

            throw new \Exception('Problem with the youtube API');

        }

    }

}


