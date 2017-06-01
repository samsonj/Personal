<?php
/*
Plugin Name: AKA Twitter
Plugin URI: http://www.akauk.com
Description: Fetch and display tweets
Version: 1.0.0
Author: Matt Cegielka
Author URI: http://www.akauk.com
*/
require_once(dirname(__FILE__) . "/twitteroauth/twitteroauth.php");

// Autoloader
spl_autoload_register(function($class) {
    // project-specific namespace prefix
    $prefix = 'Aka\\Twitter\\';

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/src/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});


function akatwitter()
{
	static $twitter = null;

	if (is_null($twitter)) {
		$twitter = new \Aka\Twitter\Twitter(
			new TwitterOAuth(
				get_option('akatwitter_consumer_key'),
				get_option('akatwitter_consumer_secret'),
				get_option('akatwitter_oauth_token'),
				get_option('akatwitter_oauth_token_secret')
			)
		);
	}

	return $twitter;
}

$twitter = akatwitter();
