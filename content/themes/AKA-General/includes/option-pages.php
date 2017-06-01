<?php

//******************************************************************************
// ADD THEME OPTION PAGE TO 'APPEARANCE' MENU
//******************************************************************************

function aka_theme_options() {
    add_theme_page( 'Theme Options', 'Theme Options', 'edit_theme_options', 'theme_options', 'aka_theme_options_page' );
}
add_action( 'admin_menu', 'aka_theme_options' );


//******************************************************************************
// REGISTER THEME OPTIONS SUPPORT
//******************************************************************************

function aka_register_settings() {
    register_setting( 'aka_theme_options', 'aka_options' );
}
add_action( 'admin_init', 'aka_register_settings' );


//******************************************************************************
// ENQUEUE SCRIPTS AND STYLES FOR THEME OPTIONS
//******************************************************************************

if (isset($_GET['page']) && $_GET['page'] == 'theme_options') {
	add_action('admin_print_scripts', 'admin_scripts');
	add_action('admin_print_styles', 'admin_styles');
}

function admin_scripts() {
	wp_enqueue_media();
	wp_register_script('theme-options-script', get_bloginfo('template_url') . '/core/js/min/theme-options.min.js', array('jquery','media-upload','thickbox'));
	wp_enqueue_script('theme-options-script');
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
}

function admin_styles() {
	wp_register_style('theme-options-styles', get_bloginfo('template_url') . '/core/css/theme-options.css');
	wp_enqueue_style('theme-options-styles');
	wp_enqueue_style('thickbox');
}


// SETUP GLOBAL Options

if( function_exists('acf_add_options_page') ) {

	$parent = acf_add_options_page(
		array(
			'page_title' => 'Site Options',
			'menu_title' => 'Site Options',
			'redirect' => true
		)
	);

	acf_add_options_sub_page( array( 'page_title' => 'General', 'menu_title' => 'General', 'parent_slug' => $parent['menu_slug'] ) );
	acf_add_options_sub_page( array( 'page_title' => 'Header', 'menu_title' => 'Header', 'parent_slug' => $parent['menu_slug'] ) );
	acf_add_options_sub_page( array( 'page_title' => 'Footer', 'menu_title' => 'Footer', 'parent_slug' => $parent['menu_slug'] ) );
	
}


function aka_theme_options_page() {
    global $aka_options, $aka_categories, $aka_layouts;

    if ( ! isset( $_REQUEST['updated'] ) )
    $_REQUEST['updated'] = false; ?>

    <div class="wrap">

	    <?php echo "<h2>" .wp_get_theme() . __( ' Theme Options' ) . "</h2>"; ?>

	    <?php if ( false !== $_REQUEST['updated'] ) : ?>
	    	<div class="message"><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>
	    <?php endif; // If the form has just been submitted, this shows the notification ?>

	    <form id="options" method="post" action="options.php">

		    <?php $settings = get_option( 'aka_options', $aka_options ); ?>

		    <?php /* This function outputs some hidden fields required by the form,
		    including a nonce, a unique number used to ensure the form has been submitted from the admin page
		    and not somewhere else, very important for security */ ?>
		    <?php settings_fields( 'aka_theme_options' ); ?>

		 	<h3>Brand Logo</h3>

		    <table cellpadding="5" cellspacing="0" border="0">

			    <tr>
			    	<th><label for="brand_logo_button">Homepage Logo:</label></th>
				    <td>
				    	<img style="display: none;" id="brand_logo" src="<?php esc_attr_e($settings['brand_logo']); ?>">
				    </td>
				    <td>
				    	<input name="aka_options[brand_logo]" type="hidden" value="<?php esc_attr_e($settings['brand_logo']); ?>" />
				    	<input id="brand_logo_button" class="button upload-image" type="button" name="aka_options[brand_logo]" value="Upload Logo" />
				    	<?php if ( $settings['brand_logo'] != '') : ?>
				    		<input id="brand_logo_remove" class="button remove-image" type="button" value="Remove" />
				    	<?php endif; ?>
				    </td>
				    <td class="hint">Used for the home page only</td>
			    </tr>

			    <tr>
			    	<th><label for="brand_logo_inside_button">Inside page Logo:</label></th>
				    <td>
				    	<img style="display: none;" id="brand_logo_inside" src="<?php esc_attr_e($settings['brand_logo_inside']); ?>">
				    </td>
				    <td>
				    	<input name="aka_options[brand_logo_inside]" type="hidden" value="<?php esc_attr_e($settings['brand_logo_inside']); ?>" />
				    	<input id="brand_logo_inside_button" class="button upload-image" type="button" name="aka_options[brand_logo_inside]" value="Upload Logo" />
				    	<?php if ( $settings['brand_logo_inside'] != '') : ?>
				    		<input id="brand_logo_inside_remove" class="button remove-image" type="button" value="Remove" />
				    	<?php endif; ?>
				    </td>
				    <td class="hint">Used for all inside pages</td>
			    </tr>

		    </table>

		 	<h3>App Icons</h3>

		    <table cellpadding="5" cellspacing="0" border="0">

			    <tr>
			    	<th><label for="fav_icon_button">Favourites Icon:</label></th>
				    <td>
				    	<?php if ( $settings['fav_icon'] != '') : ?><img id="fav_icon" src="<?php esc_attr_e($settings['fav_icon']); ?>"><?php endif; ?>
				    </td>
				    <td>
				    	<input name="aka_options[fav_icon]" type="hidden" value="<?php esc_attr_e($settings['fav_icon']); ?>" />
				    	<input id="fav_icon_button" class="button upload-image" type="button" name="aka_options[fav_icon]" value="Upload Icon" />
				    	<?php if ( $settings['fav_icon'] != '') : ?><input id="fav_icon_remove" class="button remove-image" type="button" value="Remove" /><?php endif; ?>
				    </td>
				    <td class="hint">Used in the browser address bar</td>
			    </tr>
			    <tr>
			    	<th><label for="touch_icon_button">Apple Touch Icon:</label></th>
				    <td>
				    	<?php if ( $settings['touch_icon'] != '') : ?><img id="touch_icon" src="<?php esc_attr_e($settings['touch_icon']); ?>"><?php endif; ?>
				    </td>
				    <td>
				    	<input name="aka_options[touch_icon]" type="hidden" value="<?php esc_attr_e($settings['touch_icon']); ?>" />
				    	<input id="touch_icon_button" class="button upload-image" type="button" name="aka_options[touch_icon]" value="Upload Icon" />
				    	<?php if ( $settings['touch_icon'] != '') : ?><input id="touch_icon_remove" class="button remove-image" type="button" value="Remove" /><?php endif; ?>
				    </td>
				    <td class="hint">Used when saved to Apple device home screen</td>
			    </tr>

		    </table>

		    <?php submit_button( "Save Changes", "submit primary large", "submit" ) ?>

	    </form>

    </div>

<?php 

} 