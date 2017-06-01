<?php
/**
 * AKA Cookies Shortcode class
 */
class Akacookies_Shortcode {

    public static
    $instance = null;

    /**
     * Object constructor
     */
    function __construct() {
        self::$instance = $this;
		
        // Add shortcodes
        add_shortcode('cookietable', array($this, 'display_cookie_table'));
        
    } 
    
    /**
    * Display Cookie Table
    */
    public function display_cookie_table() {
    	$cookie_table = get_option('cookie_table');
		
    	if(!empty($cookie_table)) {
    		return $cookie_table;
    	} else {
    		return "";	
    	}    
    }    
        
}