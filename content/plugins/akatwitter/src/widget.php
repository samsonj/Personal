<?php
/**
 * Implement a WP Widget for displaying a list of tweets
 */
class akaTwitter extends WP_Widget {
    /** constructor */
    function __construct(){
        parent::__construct('akatwitter', 'AKA Twitter');
        wp_enqueue_script("twitter-widgets", '//platform.twitter.com/widgets.js', null, 1, true);
    }
