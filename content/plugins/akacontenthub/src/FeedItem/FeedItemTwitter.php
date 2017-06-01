<?php

namespace ContentHub\FeedItem;

class FeedItemTwitter extends FeedItem {

    public $twitter;

	public function __construct($access_token, $access_token_secret, $consumer_key, $consumer_secret) {   
    	parent::__construct();
        $settings = array(
            'oauth_access_token' => $access_token,
            'oauth_access_token_secret' => $access_token_secret,
            'consumer_key' => $consumer_key,
            'consumer_secret' => $consumer_secret
        );
        $this->twitter =  new \TwitterAPIExchange($settings);
    }

    public function getTweet($id) {
    	$response;
        
        $url = 'https://api.twitter.com/1.1/statuses/show.json';
        $getfield = '?id='.$id;
        $requestMethod = 'GET';

        $response = $this->twitter->setGetfield($getfield)
            ->buildOauth($url, $requestMethod)
            ->performRequest();

        $response = json_decode($response);

        return $response;
    }

}
