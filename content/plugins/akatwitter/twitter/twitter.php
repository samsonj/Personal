<?php

function aka_twitter_get($twitterType, $twitter_api, $params = array())
{
    switch ($twitterType) {
        case 'usersHash':

        $parameters = array('screen_name' => $params['user']);
        if($exclude_replies == true) {
            $parameters['exclude_replies'] = true;
        } else {
            $parameters['count'] = $twitterTweets;
        }
        $tweets = $twitter_api->get('statuses/user_timeline', $parameters);
        $tweets_to_keep = array();
        $twitterTweets = count($tweets);

        $twitterHashs = explode(' ', $twitterHash);

        foreach($twitterHashs as $twitterHashtag) {
            for($x=0;$x<$twitterTweets;$x++) {
             if (strpos($tweets[$x]->text , $twitterHashtag ) !== false){
                $tweets_to_keep[] = $tweets[$x];
                break;
            }
        }
    }
    $tweets = $tweets_to_keep;
    break;
    case 'user':
    $parameters = array('screen_name' => $params['user']);
    if($exclude_replies == true) {
        $parameters['exclude_replies'] = true;
    } else {
        $parameters['count'] = $twitterTweets;
    }
    $tweets = $twitter_api->get('statuses/user_timeline', $parameters);

    break;
    case 'faves':
    $tweets = $twitter_api->get('favorites/list', array('screen_name' => $params['user'], 'count' => $twitterTweets));
    break;
    case 'hash':
    $tweets = $twitter_api->get('search/tweets', array('q' => $twitterHash, 'count' => $twitterTweets));
    if (!$tweets->errors) {
        $tweets = $tweets->statuses;
    }

    break;
}
}
