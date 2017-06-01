<?php
define('AKASETTINGS_PLUGIN_DIR', dirname(__FILE__));
define('AKASETTINGS_PLUGIN_URL', plugin_dir_url( __FILE__ ));
/* 
Plugin Name: AKA Settings
Plugin URI: http://www.akauk.com 
Description: A plugin which adds a bunch of settings in admin, bringing various functionalities (cookies notification, custom administration, shortcodes etc...)
Version: 0.1
Author: AKA
Author URI: http://www.akauk.com/
*/

/* Configure autoloader */
spl_autoload_register('Akasettings::autoload');

/**
 * Base AKA CMS class
 */
class Akasettings {
	public static
    $instance 		= null,
    $file     		= __FILE__;

    function __construct() {
        self::$instance = $this;
        add_action('init', array($this, 'init'));

        // Deactivate cookie table generation cron job on plugin deactivation
        register_deactivation_hook(__FILE__, array($this, 'cookie_table'));
    }

    public function init()
    {
        $akacms_shortcodes = new Akacms_Shortcodes();
        $akacms_admin = new Akacms_Admin();
        $akacustomadmin_admin = new Akacustomadmin_Admin();
        $akacookies_admin = new Akacookies_Admin();
        $akacookies_shortcode = new Akacookies_Shortcode();
        $akacookies_frontend = new Akacookies_Frontend();
        
        // Initialisation
        add_action('admin_menu', array($this, 'aka_settings_admin_menu'));
        add_action('admin_init', array($this, 'admin_init'));
    }
    
    public static function autoload( $classname ) {    
        if ( 
        	'Akacms' !== mb_substr( $classname, 0, 6 ) &&
        	'Akacookies' !== mb_substr( $classname, 0, 10 ) &&
        	'Akacustomadmin' !== mb_substr( $classname, 0, 14 )
        )
            return;

        $filename = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . str_replace( '_', DIRECTORY_SEPARATOR, $classname ) . '.php';

        if ( file_exists( $filename ) )
            require $filename;
    }

    public static function getFile() {
        return self::$file;
    }
    
    public function aka_settings_admin_menu() {
    	
    	// Creating AKA CMS Admin object to call the function since it will be the first item in the submenu (General)
    	$akacms_admin = new Akacms_Admin();
	add_menu_page('AKA Settings > General', 'AKA Settings', 'manage_options', 'akasettings_general', array($akacms_admin, 'aka_cms_admin_page'), 'dashicons-lightbulb', 99);
		
    }
    
	public function admin_init() {
		wp_enqueue_style('akasettings-admin', AKASETTINGS_PLUGIN_URL.'/library/css/admin.css');
	}

    function cookie_table() {
        wp_clear_scheduled_hook('generate_cookie_table_hook');
    }


}

// Let's do it
$akasettings = new Akasettings();

function aka_share($network, $url = '', $description = '', $title = '') {
    return Akacms_Shortcodes::get_share_url($network, $url, $description, $title);
}


?>
