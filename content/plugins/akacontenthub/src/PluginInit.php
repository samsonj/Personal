<?php

namespace ContentHub;

/*
PLuginInit class for plugin activation, deactivation actions
*/

class PluginInit {

	public function __construct($file)
    {
        register_activation_hook($file, array($this, 'install'));
    }

    public static function install()
    {
        /*
        Create database content_hub_item table
        */
        global $wpdb;
       $sql = "
            CREATE TABLE `".$wpdb->prefix."contenthub_items` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `type` varchar(255) NOT NULL,
                `description` text NULL,
                `username` text NULL,
                `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                `image` text NULL,
                `link` text NULL,
                `menu_order` int(11) NULL ,    
                `css_classes` varchar(255) NULL,
                PRIMARY KEY (`id`),
                INDEX (`type`)
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
        ";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

}
