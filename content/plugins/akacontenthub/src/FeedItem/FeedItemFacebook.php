<?php

namespace ContentHub\FeedItem;
use Facebook\Facebook;


class FeedItemFacebook extends FeedItem {

    public $facebook;
    public $access_token;
    
	public function __construct($app_id, $app_secret, $access_token ) {   
    	parent::__construct();

      $this->facebook  = new Facebook([
          'app_id' => $app_id,
          'app_secret' => $app_secret,
          'default_graph_version' => 'v2.5',
      ]);

      $this->access_token = $access_token;

    }

    public function getStatus($id, $feedType) { 
      $response;   
      $userID ;
      if ($feedType == "posts"): 
          //Get User Id
          try {
              $response =  $this->facebook->get('/me?fields=id', $this->access_token);
          } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
          } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
          }
          $user   = $response->getGraphUser();
          $userID = $user['id']."_";  
          $request =  $this->facebook->request('GET', '/'.$userID.$id.'?access_token='.$this->access_token .'&fields=id,message,from,name,updated_time,created_time,link,source');
          else:
             $request =  $this->facebook->request('GET', '/'.$id.'?access_token='.$this->access_token .'&fields=id,from,name,updated_time,created_time,link,source');
    
      endif;

        // Send the request to Graph   
        try {
          $response = $this->facebook->getClient()->sendRequest($request);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
          // When Graph returns an error
          echo 'Graph returned an error: ' . $e->getMessage();
          exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
          // When validation fails or other local issues
          echo 'Facebook SDK returned an error: ' . $e->getMessage();
          exit;
        }

        $graphNode = $response->getGraphNode();
        return  $graphNode;
    }

}
