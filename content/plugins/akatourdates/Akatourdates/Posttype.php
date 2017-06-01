<?php
/**
 * Aka Tour Dates Post type class
 */
class Akatourdates_Posttype {

    public static
    $instance = null;

    /**
     * Object constructor
     */
    function __construct() {
        self::$instance = $this;
        $this->register_entry_type();
    }

    
	public function register_entry_type() {
	
		// Basic post type
        register_post_type('tourdate', array(
            'labels' => array(
                'name' => 'Tour Dates',
                'singular_name' => 'Tour Date'
            ),
            'description' => 'Tour dates',
            'public' => true,
            'supports' => array('title', 'editor'),
            'rewrite' => array(
                'slug' => 'tourdate',
                'with_front' => true
            ),
            'has_archive' => true
        ));
		
	}
	
}