<?php
/*
Plugin Name: AKA Content Hub
Plugin URI: http://www.akauk.com
Description: Allows the creation of a feed containing items from various networks
Version: 0.1
Author: AKA
Author URI: http://www.akauk.com/
*/

define('CONTENTHUB_DIR', __DIR__);
define('CONTENTHUB_URL', plugin_dir_url( __FILE__ ));

include 'vendor/autoload.php';

$ContentHubPluginInit = new ContentHub\PluginInit(__FILE__);
$ContentHubAdmin = new ContentHub\Admin();

// Declare global variables to be used everywhere
function get_contenthub_feed($limit = -1, $offset = -1, $feedtype = "" , $fromdate = "") {
	$dbObj = new ContentHub\Database();
	return $dbObj->get_contenthub_feed($limit, $offset, $feedtype, $fromdate);
}

function count_contenthub_feed_items($feedtype = "")
{
    $dbObj = new ContentHub\Database();
    return $dbObj->count_contenthub_feed_items($feedtype);
}

