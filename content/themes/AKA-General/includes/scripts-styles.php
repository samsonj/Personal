<?php


//******************************************************************************
// SCRIPTS & ENQUEUEING
//******************************************************************************

add_action( 'wp_enqueue_scripts', 'aka_scripts_and_styles', 999 );

function aka_scripts_and_styles() { 
	global $wp_styles;    
	if (!is_admin()) {

		// Load asset manifest
		$assetstr = file_get_contents(dirname(dirname(__FILE__))."/build/manifest.json");
		$assets = json_decode($assetstr, true);
		$assets     = array(
			'css' => '/build/css/styles.min.css' . '?' . $assets['build/css/styles.min.css']['hash'],
			'js'  => '/build/js/scripts.min.js' . '?' . $assets['build/js/scripts.min.js']['hash'],
		);

		wp_register_script( 'modernizr', get_stylesheet_directory_uri() . '/build/js/modernizr.min.js' ); 

		wp_register_style( 'AKA-stylesheet', get_stylesheet_directory_uri() . $assets['css'], array(), '', 'all' );
		wp_enqueue_style( 'AKA-stylesheet' );

		wp_deregister_script( 'jquery' );  
		wp_register_script( 'jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.2/jquery.min.js', false, '1.10.0', true);

		// This is the concatinated set of scripts (Keeps down HTML requests)
		wp_register_script( 'scripts', get_stylesheet_directory_uri() . $assets['js'], array(), '', true );

		// Do it.
		wp_enqueue_script( 'modernizr' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'scripts' );

	}
}
