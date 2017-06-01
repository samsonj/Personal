<?php
define('WP_DEBUG', true);
if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		} );
	return;
}

require_once( 'includes/core.php' ); // This is all the core base functions in one place.
require_once( 'includes/option-pages.php' ); // Add theme options and general options pages.
require_once( 'includes/navwalker.php' ); // Add a navigation walker for the main menu.
require_once( 'includes/scripts-styles.php' ); // Enqueue scripts and styles.
require_once( 'includes/menus.php' ); // Add AKA Menu functions.
require_once( 'includes/pagination.php' ); // Add pagination function.
require_once( 'includes/custom-post-types.php' ); // Create useful custom post types. Can be changed or removed
require_once( 'includes/default-pages.php' ); // Create default pages on theme activation (and only the first time).

Timber::$dirname = array('views');

class AKABaseShow extends TimberSite {

	function __construct() {
		add_filter( 'timber_context', array( $this, 'add_to_context' ) );
		add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
		parent::__construct();
	}

	// Register variables for using in twig template file & Add to context so it can be accessed globally.
	function add_to_context( $context ) {
		$content[''] = new TimberMenu('');
		//globally assigned theme options
		$aka_settings = get_option('aka_options');
		$context['aka_settings'] = $aka_settings;
		$context['brand_logo'] = $aka_settings['brand_logo'];
		$context['brand_logo_inside'] = ( $aka_settings['brand_logo_inside'] == '' ) ? $context['brand_logo'] : $aka_settings['brand_logo_inside'];
		$context['domain']      =   get_bloginfo('siteurl');
		$context['ajax_url']    = 	admin_url('admin-ajax.php');
		$context['notes'] = 'These values are available everytime you call Timber::get_context();';
		$context['menu'] = new TimberMenu();
		$context['site'] = $this;
		return $context;
	}

	function add_to_twig( $twig ) {
		/* this is where you can add your own fuctions to twig */
		$twig->addExtension( new Twig_Extension_StringLoader() );
		$twig->addFilter( 'str_repeat', new Twig_Filter_Function( 'str_repeat' ) );
		return $twig;
	}

}

new AKABaseShow();
