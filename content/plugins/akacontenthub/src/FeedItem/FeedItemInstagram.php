<?php

namespace ContentHub\FeedItem;
use MetzWeb\Instagram\Instagram;

class FeedItemInstagram extends FeedItem {

    public $instagram;

	public function __construct($instagram_api_key, $instagram_api_secret, $instagram_access_token) {   
    	parent::__construct();
        $this->instagram = new Instagram(array(
            'apiKey'      => $content_hub_settings['instagram_api_key'],
            'apiSecret'   => $content_hub_settings['instagram_api_secret'],
            'apiCallback' => admin_url( 'admin.php?page=content_hub_settings&code_type=instagram')
        ));
        $this->instagram->setAccessToken($instagram_access_token);
    }

    public function getMedia($id) {
    	
        $response = json_decode(file_get_contents('https://api.instagram.com/v1/media/shortcode/'.$id.'?access_token='.$this->instagram->getAccessToken()));
        return $response;

    }

}
