<?php
define('AKATOURDATES_PLUGIN_DIR', dirname(__FILE__));
define('AKATOURDATES_PLUGIN_URL', plugin_dir_url( __FILE__ ));
/* 
Plugin Name: AKA Tour Dates
Plugin URI: http://www.akauk.com 
Description: A plugin that will allow for users to add tour date locations and render them on a map, or in a table
Version: 0.1
Author: AKA
Author URI: http://www.akauk.com/
*/

/* Configure autoloader */
spl_autoload_register('Akatourdates::autoload');

/**
 * Base AKA Tour Dates class
 */
class Akatourdates {
	public static
    $instance 	= null,
    $file     	= __FILE__;

    function __construct() {
        self::$instance = $this;
        add_action('init', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'install'));
    }

	/**
     * Wordpress init callback
     */
    public function init()
    {

        /*
        Each class here implements a part of the plugin - names should be 
        fairly self-explanatory
        */
        $shortcodes = new Akatourdates_Shortcodes();
        $posttype = new Akatourdates_Posttype();
        $admin = new Akatourdates_Admin();
    }

    /**
     * Actions we only need to run at install time
     */
    public static function install()
    {
        /*
        Create database city table
        */
        global $wpdb;
        $sql = "
        CREATE TABLE `".$wpdb->prefix."tourdatelocation` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `coordinates` text,
            PRIMARY KEY (`id`),
            UNIQUE KEY (`name`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
        
        INSERT INTO `akawp_tourdatelocation` (`name`, `coordinates`)
		VALUES ('London', '{\"x\":337,\"y\":418}'),
		('Leeds', '{\"x\":294,\"y\":304}'),
		('Manchester', '{\"x\":266,\"y\":315}'),
		('Brighton', '{\"x\":336,\"y\":457}'),
		('Belfast', '{\"x\":152,\"y\":258}'),
		('Dublin', '{\"x\":143,\"y\":336}'),
		('Edinburgh', '{\"x\":249,\"y\":194}'),
		('Blackpool', '{\"x\":250,\"y\":301}'),
		('Bradford', '{\"x\":285,\"y\":304}'),
		('Bath', '{\"x\":263,\"y\":429}'),
		('Birmingham', '{\"x\":276,\"y\":366}'),
		('Aberdeen', '{\"x\":\"270\",\"y\":\"131\"}'),
		('Bristol', '{\"x\":\"255\",\"y\":\"424\"}'),
		('Cambridge', '{\"x\":\"343\",\"y\":\"389\"}'),
		('Canterbury', '{\"x\":\"374\",\"y\":\"438\"}'),
		('Cardiff', '{\"x\":\"232\",\"y\":\"424\"}'),
		('Chester', '{\"x\":\"243\",\"y\":\"333\"}'),
		('Chichester', '{\"x\":\"315\",\"y\":\"455\"}'),
		('Coventry', '{\"x\":284,\"y\":374}'),
		('Derby', '{\"x\":287,\"y\":350}'),
		('Dundee', '{\"x\":\"252\",\"y\":\"164\"}'),
		('Eastbourne', '{\"x\":348,\"y\":457}'),
		('Exeter', '{\"x\":\"226\",\"y\":\"462\"}'),
		('Glasgow', '{\"x\":\"208\",\"y\":\"198\"}'),
		('Gloucester', '{\"x\":\"266\",\"y\":\"405\"}'),
		('Hull', '{\"x\":\"332\",\"y\":\"302\"}'),
		('Lancaster', '{\"x\":250,\"y\":295}'),
		('Leicester', '{\"x\":\"294\",\"y\":\"361\"}'),
		('Lincoln', '{\"x\":\"320\",\"y\":\"321\"}'),
		('Liverpool', '{\"x\":246,\"y\":322}'),
		('Newcastle', '{\"x\":\"284\",\"y\":\"240\"}'),
		('Norwich', '{\"x\":\"384\",\"y\":\"369\"}'),
		('Nottingham', '{\"x\":297,\"y\":347}'),
		('Oxford', '{\"x\":\"302\",\"y\":\"410\"}'),
		('Plymouth', '{\"x\":\"206\",\"y\":\"478\"}'),
		('Portsmouth', '{\"x\":\"306\",\"y\":\"453\"}'),
		('Salisbury', '{\"x\":\"278\",\"y\":\"443\"}'),
		('Sheffield', '{\"x\":\"291\",\"y\":\"320\"}'),
		('Southampton', '{\"x\":\"291\",\"y\":\"447\"}'),
		('Stoke-on-Trent', '{\"x\":\"265\",\"y\":\"351\"}'),
		('Sunderland', '{\"x\":\"287\",\"y\":\"234\"}'),
		('Swansea', '{\"x\":\"213\",\"y\":\"419\"}'),
		('Wolverhampton', '{\"x\":268,\"y\":359}'),
		('Worcester', '{\"x\":267,\"y\":388}'),
		('York', '{\"x\":\"300\",\"y\":\"293\"}');
		('Belfast', '{\"x\":\"152\",\"y\":\"258\"}');
		";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    /**
     * psr0 Class autoloader
     */
    public static function autoload( $classname ) {    
        if ( 'Akatourdates' !== mb_substr( $classname, 0, 12 ) )
            return;

        $filename = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . str_replace( '_', DIRECTORY_SEPARATOR, $classname ) . '.php';
        if ( file_exists( $filename ) )
            require $filename;
    }

    /**
     * Return the base plugin filename
     */
    public static function getFile() {
        return self::$file;
    }
}

// Let's do it
$akacomp = new Akatourdates();


?>