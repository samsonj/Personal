<?php

namespace Aka\Twitter;

use TwitterOauth;
use \DateTime;

class Twitter {

    const TYPE_USERHASH = 'userhash';
    const TYPE_USER     = 'user';
    const TYPE_HASH     = 'hash';
    const TYPE_FAVES    = 'faves';

    private $api;
    private $admin;
    private $cachepath;
    private $defaults = array(
        'tweets' => 5,
        'follow' => true,
        'showbuttons' => true
    );

    public function __construct(TwitterOauth $twitter_api, $cachepath = null)
    {

        if (is_null($cachepath)) {
            $cachepath = WP_CONTENT_DIR.DIRECTORY_SEPARATOR."cache".DIRECTORY_SEPARATOR."akatwitter";
        }
        $this->cachepath = $cachepath;
        if (!file_exists($this->cachepath)) {
            @mkdir($this->cachepath, 0755, true);
        }

        $this->api = $twitter_api;
        if (is_admin()) {
            $this->admin = new Admin();
        }

        add_action('init', array($this, 'init'));
    }

    public function init()
    {
        add_shortcode('akatwitter', array($this, 'shortcode'));
        add_shortcode('akaTwitter', array($this, 'shortcode'));
    }

    public function get($type, $params = array())
    {

        $params = array_map('strtolower', $params);

        $cachekey = $this->getCacheKey($type, $params);
        if (!$cachekey) {
            error_log("Error generating cache key - no tweets will be fetched");
            return array();
        }

        $tweets = $this->getFromCache($cachekey, 600);

        if (!$tweets) {
            $tweets = $this->getFromApi($type, $params);
            if (is_array($tweets) && count($tweets)) {
                $this->setCache($cachekey, $tweets);
            }
        }

        return $tweets;
    }

    public function getFromCache($cachekey)
    {
        $filename = $this->cachepath.DIRECTORY_SEPARATOR.$cachekey;
        if (filemtime($filename) < time() - 600) return array();

        $json = file_get_contents($filename);
        if (!$json || !($tweets = json_decode($json))) {
            return array();
        }
        return $tweets;
    }

    public function setCache($cachekey, $tweets)
    {
        $filename = $this->cachepath.DIRECTORY_SEPARATOR.$cachekey;
        $json = json_encode($tweets);
        file_put_contents($this->cachepath.DIRECTORY_SEPARATOR.$cachekey, $json);
    }

    public function getCacheKey($type, $params)
    {
        if (empty($type) || !is_array($params) || !count($params)){
            return false;
        }

        switch ($type) {
            case self::TYPE_USERHASH:
                return "akatwitter_userhash".$params['user'].preg_replace("/[^a-z0-9]/", '', strtolower($params['hash'])).$params['tweets'];
                break;
            case self::TYPE_USER:
                return "akatwitter_user".$params['user'].$params['tweets'];
                break;
            case self::TYPE_HASH:
                return "akatwitter_hash".preg_replace("/[^a-z0-9]/", '', strtolower($params['hash']));
                break;
            case self::TYPE_FAVES:
                return "akatwitter_faves".$params['user'];
                break;
        }
        return false;
    }

    public function getFromApi($type, $params = array())
    {
        $type = strtolower((string)$type);

        switch ($type) {
            case self::TYPE_USERHASH:
                return $this->getUserHash($params);
                break;
            case self::TYPE_USER:
                return $this->getUser($params);
                break;
            case self::TYPE_HASH:
                return $this->getHash($params);
                break;
            case self::TYPE_FAVES:
                return $this->getFaves($params);
                break;
        }
    }

    public function getUserHash($params = array())
    {

        $parameters = array(
            'screen_name' => $params['user'],
            'exclude_replies' => (boolean)$params['exclude_replies'],
            'count' => $params['tweets']*2
        );

        $hashes = is_array($params['hashes']) ? $params['hashes'] : explode(' ', $params['hashes']);

        $tweets = $this->api->get('statuses/user_timeline', $parameters);

        $tweets_to_keep = array();

        foreach ($tweets as $tweet) {
            foreach ($hashes as $hash) {
                if (stripos($tweet->text, $hash) !== false) {
                    $tweets_to_keep [] = $tweet;
                }
            }
        }
        return array_slice($tweets, 0, $params['tweets']);
    }

    public function getUser($params=array())
    {
        $parameters = array(
            'screen_name' => $params['user'],
            'exclude_replies' => (boolean)$params['exclude_replies'],
            'count' => $params['tweets']
        );

        $tweets = $this->api->get('statuses/user_timeline', $parameters);

        return $tweets;

    }

    public function getFaves($params)
    {
        $tweets = $this->api->get('favorites/list', array('screen_name' => $params['user'], 'count' => $params['tweets']));
        return $tweets;
    }

    public function getHash($params)
    {
        $tweets = $this->api->get('search/tweets', array('q' => $params['hash'], 'count' => $$params['tweets']));
    }

/*
    [akaTwitter
        showtitle="0"
        title="akaTwitter"
        text=""
        showUser=""
        showImage=""
        user="akaconnect"
        hash=""
        tweets="5"
        follow="1"
        showbuttons="1"
        classes="classy"]
*/
    public function render($tweets, $options)
    {

        $params = array_map('strtolower', $params);

        if ($options['showbuttons'] || $options['follow']):
            // add javascript
            add_action('wp_footer', function(){
                wp_register_script('twitterFollow', '//platform.twitter.com/widgets.js', array(), '1.0', true);
                wp_print_scripts('twitterFollow');
            });
        endif;
        ?>
        <div class="<?= esc_attr($options['classes']) ?>">

            <?php if ($options['showimage']): ?>

                <img src="<?= esc_attr($tweets[0]->user->profile_image_url) ?>" alt="<?= esc_attr($options['user']) ?>" class="twitterImage" />
            <?php endif; ?>

            <?php if ($options['showuser']): ?>
                <span class="twitterUser"><?= esc_html($options['user']); ?></span>
            <?php endif; ?>

            <?php if (strlen(trim($options['text']))): ?>
                <?= esc_html($options['text']); ?>
            <?php endif; ?>

            <?php if (!count($tweets)): ?>

                <p>Sorry, tweets are not available at the moment. Hopefully they will be back up soon</p>

            <?php else: ?>

                <ul class="tweets">
                <?php foreach ($tweets as $tweet): ?>
                    <li class="tweet">

                        <?= $options['before']; ?>

                        <p><?= $this->formatLinks($tweet->text) ?></p>

                        <?php if ($options['showuserlink']): ?>
                            <ul>
                                <li class="user">
                                    <a href="http://twitter.com/<?=esc_attr($tweets[$x]->user->screen_name) ?>" target="_blank">'.<?=esc_html($tweets[$x]->user->name)?></a>
                                </li>
                            </ul>
                        <?php endif; ?>

                        <?php if ($options['showbuttons']): ?>
                            <?php
                            $retweet_url = 'http://twitter.com/intent/retweet?tweet_id='.$tweet->id_str;
                            $reply_url = 'http://twitter.com/intent/tweet?in_reply_to='.$tweet->id_str;
                            ?>
                            <ul class="tweetBtns">
                                <li class="retweet"><a href="<?= esc_attr($retweet_url); ?>" target="_blank">Retweet</a></li>
                                <li class="reply"><a href="<?= esc_attr($reply_url); ?>" target="_blank">Reply</a></li>
                                <li class="sent"><?= esc_html($this->formatDateHuman($tweet->created_at))?></li>
                                <?php if ($options['showvia']): ?>
                                    <li class="via">via <?= html_entity_decode($tweets[$x]->source) ?></li>
                                <?php endif; ?>
                            </ul>
                        <?php endif; ?>

                        <?= $options['after']; ?>

                    </li>
                <?php endforeach; ?>
                </ul>

            <?php endif; ?>
            <?php
            if($options['follow']):
            ?>
                <div id="followBtn">
                    <a class="twitter-follow-button" href="http://twitter.com/<?= esc_attr($options['user']) ?>" data-button="grey" data-text-color="#FFFFFF" data-link-color="#00AEFF">
                        Follow @<?=esc_html($options['user']); ?>
                    </a>
                </div>
            <?php
            endif;
            ?>
        </div>
        <?php
    }

    public function getType($params)
    {
        $user = trim($params['user']);
        $hash = trim($params['hash']);
        $faves = (boolean)$params['faves'];

        $type = strtolower(trim($params['type']));

        // if Type is specified explicitly, and matches an existing type, use it
        if ($type == TYPE_USERHASH) return TYPE_USERHASH;
        if ($type == TYPE_USER) return TYPE_USER;
        if ($type == TYPE_HASH) return TYPE_HASH;
        if ($type == TYPE_FAVES) return TYPE_FAVES;

        // If no valid type is given, try and work it out.
        if(strlen($user) > 0 && strlen($hash) > 0 ){
            return self::TYPE_USERHASH;
        }
        if (strlen($user) > 0){
            $rtype = self::TYPE_USER;
            if ($faves) {
                $rtype = self::TYPE_FAVES;
            }
        }
        return $rtype;

        if(strlen($hash) < 0) {
            return self::TYPE_HASH;
        }

        return false;
    }

    public function shortcode($params)
    {
        $options = shortcode_atts( array(
            'showtitle'         => '0',
            'title'             => 'akaTwitter',
            'text'              => '',
            'showuser'          => '0',
            'showimage'         => '0',
            'user'              => 'akaconnect',
            'exclude_replies'   => '0',
            'faves'             => '0',
            'hash'              => '',
            'tweets'            => '5',
            'follow'            => '0',
            'showbuttons'       => '0',
            'classes'           => ''
        ), $params);
        $tweets = $this->get($this->getType($options), $options);
        return $this->render($tweets, $options);
    }


    public function formatLinks($text)
    {
        // convert URLs into links
        $text = preg_replace("#(https?://([-a-z0-9]+\.)+[a-z]{2,5}([/?][-a-z0-9!\#()/?&+]*)?)#i", "<a href='$1' target='_blank'>$1</a>",$text);
        // convert protocol-less URLs into links
        $text = preg_replace("#(?!https?://|<a[^>]+>)(^|\s)(([-a-z0-9]+\.)+[a-z]{2,5}([/?][-a-z0-9!\#()/?&+.]*)?)\b#i", "$1<a href='http://$2'>$2</a>",$text);

        // convert @mentions into follow links
        $text = preg_replace("#(?!https?://|<a[^>]+>)(^|\s)(@([_a-z0-9\-]+))#i", "$1<a href=\"http://twitter.com/$3\" title=\"Follow $3\" target=\"_blank\">@$3</a>",$text);
        // convert #hashtags into tag search links
        $text = preg_replace("#(?!https?://|<a[^>]+>)(^|\s)(\#([_a-z0-9\-]+))#i", "$1<a href='http://twitter.com/search?q=%23$3' title='Search tag: $3' target='_blank'>#$3</a>",$text);

        return $text;
    }

    public function formatDateHuman($datestring)
    {
        $now = new DateTime(current_time('Y-m-d\TH:i:sP'));
        $posted = new DateTime($datestring);

        $interval = $now->diff($posted);

        // Years
        if ($interval->y > 1) {
            return "{$interval->y} years ago";
        }
        if ($interval->y == 1) {
            return "1 year ago";
        }

        // Months
        if ($interval->m > 1) {
            return "{$interval->m} months ago";
        }
        if ($interval->m == 1) {
            return "1 month ago";
        }

        // Weeks
        if ($interval->d >= 7) {
            $w = floor($interval->d / 7);
            if ($w == 1) {
                return '1 week ago';
            }
            return "{$w} weeks ago";
        }

        // Days
        if ($interval->d > 1) {
            return "{$interval->d} days ago";
        }
        if ($interval->d == 1) {
            return "1 day ago";
        }

        // Hours
        if ($interval->h > 1) {
            return "{$interval->h} hours ago";
        }
        if ($interval->h == 1) {
            return "1 hour ago";
        }

        // Minutes
        if ($interval->i > 1) {
            return "{$interval->i} minutes ago";
        }
        if ($interval->i == 1) {
            return "1 minute ago";
        }

        // Maybe this should be different
        return "Just now";
    }
}
