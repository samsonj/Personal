<?php
/**
* AKA Custom Admin admin page class
*/
class Akacustomadmin_Admin {

    public static
    $instance = null;

    /**
    * Object constructor
    */
    function __construct() {
        self::$instance = $this;

		// Add javascript and CSS
		add_action( 'admin_enqueue_scripts',  array($this, 'include_js'));
		
		// Add submenu and page
		add_action('admin_menu', array($this, 'admin_menu'), 11);
    	
		// Include login style
		add_action('login_head', array($this, 'login_head'));
		
		// Show development mode text and overlay if necessary
		add_action('admin_head', array($this, 'admin_js') );
		
		// Changed logo url on login page
		add_filter( 'login_headerurl', array($this, 'login_headerurl'));
		
		// Changed footer content
		add_filter('admin_footer_text', array($this, 'admin_footer_text'));
		
		// Compress image uploads aggressively when the filesize is large
		add_action('wp_handle_upload', array($this, 'process_images'));
		
		// Clear global page, object, database and minify caches when saving a post
		add_action('save_post', array($this, 'save_clear_cache'));
		
		// Strip problematic space character types from post content when saving 
		add_action('wp_insert_post_data', array($this, 'custom_admin_strip_stupid_unicode'), 10, 2);
		
		// Hook into the save_post action and send notifications when a post has been saved - keeping an eye on users!		
		add_action('save_post', array($this, 'save_and_notify'));
		/* 	
			other potential actions to hook into
			add_action('future_to_publish','save_and_notify');
			add_action('new_to_publish','save_and_notify');
			add_action('draft_to_publish','save_and_notify'); 
		*/
		 
		// Theme style in TinyMCE editor
		if (get_option('use_theme_stylesheet_mce')) {
		    add_action('init', array($this, 'mce_stylesheet'));
		}
		
		// Make stip unicode activated by default
		register_activation_hook(__FILE__, array($this, 'activate'));

	
    }

    public function login_head()
    {
		wp_enqueue_style( 'login_stylesheet', AKASETTINGS_PLUGIN_URL.'library/css/login.css');	
    }

	function login_headerurl() { return 'http://www.akauk.com/'; }

	function admin_footer_text() {
		echo '<a href="http://www.akauk.com/" target="_blank"><img src="'.AKASETTINGS_PLUGIN_URL.'library/images/aka-mini.png"></a> If you need support, <a href="mailto:webteam@akauk.com">contact us</a>';
	}

	function mce_stylesheet() {
    	add_editor_style('style.css');
    }

    function activate() {
		if (get_option('strip_stupid_unicode') === false) {
	        update_option('strip_stupid_unicode', '1');
	    }
	}

	public function include_js() {
		//wp_enqueue_script('akapollquiz-admin', AKASETTINGS_PLUGIN_URL.'/library/scripts/akapollquiz-admin.js', array('jquery'));
	}
	
	public function admin_menu() {
		add_submenu_page('akasettings_general', 'AKA Settings > Admin', 'Admin', 'manage_options', 'akasettings_customadmin', array($this, 'aka_custom_admin_page'));
	}
	
	public function aka_custom_admin_page() {
		
		$default_text = "The site is on development mode. If you need to make updates, please contact aka Digital Creative Services. Thanks!";
		
		// handle submits
		if(isset($_POST['submit'])) {
    	    update_option('strip_stupid_unicode', filter_var($_POST['strip_stupid_unicode'], FILTER_SANITIZE_STRING));
			update_option('image_compressing', filter_var($_POST['image_compressing'], FILTER_SANITIZE_STRING));
			update_option('save_clear_cache', filter_var($_POST['save_clear_cache'], FILTER_SANITIZE_STRING));
    	    update_option('development_mode', filter_var($_POST['development_mode'], FILTER_SANITIZE_STRING));
			update_option('use_theme_stylesheet_mce', filter_var($_POST['use_theme_stylesheet_mce'], FILTER_SANITIZE_STRING));
			update_option('aka_update_notifications', filter_var($_POST['update_notifications'], FILTER_SANITIZE_STRING));
			update_option('aka_update_notification_recipients', filter_var($_POST['update_notification_recipients'], FILTER_SANITIZE_STRING));
		
			//FILTER
			if(is_array($_POST['update_notification_users'])){
				$user_ids = filter_var_array(array_keys($_POST['update_notification_users']), FILTER_VALIDATE_INT);
				update_option('aka_update_notification_users', $user_ids);
			} else {
				update_option('aka_update_notification_users', array());
			}
		
			if(is_array($_POST['aka_restricted_users'])){
				$user_ids = filter_var_array(array_keys($_POST['aka_restricted_users']), FILTER_VALIDATE_INT);
				update_option('aka_restricted_users', $user_ids);
			} else {
				update_option('aka_restricted_users', array());
			}
		
			if($_POST['development_mode'] == 1 && $_POST['development_mode_text'] == '') {
				$_POST['development_mode_text'] = $default_text;
			}
		
		
			update_option('development_mode_text', filter_var($_POST['development_mode_text'], FILTER_SANITIZE_STRING));
		}
		
		// get the data
    	$strip_stupid_unicode = get_option('strip_stupid_unicode');
		$image_compressing = get_option('image_compressing');
		$save_clear_cache = get_option('save_clear_cache');
		$restricted_users = get_option('aka_restricted_users');
		$development_mode = get_option('development_mode');
		$development_mode_text = get_option('development_mode_text');
    	$use_theme_stylesheet_mce = get_option('use_theme_stylesheet_mce');
		
		$update_notifications = get_option('aka_update_notifications');
		$update_notification_users = get_option('aka_update_notification_users');
		$update_notification_recipients = get_option('aka_update_notification_recipients');
		
		?>
		<div class="wrap">
			<form method="post" action="admin.php?page=akasettings_customadmin">
				<h2>Admin AKA Settings</h2>
			
    		    <p>
    		        <input type="checkbox" id="strip_stupid_unicode" name="strip_stupid_unicode" value="1" <?php if($strip_stupid_unicode == 1){ echo 'checked'; } ?> />
    		        &nbsp;&nbsp;
    		        <label for="strip_stupid_unicode">Strip especially problematic Unicode characters from post content.</label>
    		    </p>
			
    		    <p>
    		        <input type="checkbox" id="use_theme_stylesheet_mce" name="use_theme_stylesheet_mce" value="1" <?php if($use_theme_stylesheet_mce){ echo 'checked'; } ?> />
    		        &nbsp;&nbsp;
    		        <label for="use_theme_stylesheet_mce">Use the theme's stylesheet in rich text editors</label>
    		    </p>
			
				<p>
					<input type="checkbox" id="image_compressing" name="image_compressing" value="1" <?php if($image_compressing == 1){ echo 'checked'; } ?> />
					&nbsp;&nbsp;
					<label for="image_compressing">Compress image when uploading any file bigger than 0.5MB.</label>
				</p>
				<p>
					<input type="checkbox" id="save_clear_cache" name="save_clear_cache" value="1" <?php if($save_clear_cache == 1){ echo 'checked'; } ?> />
					&nbsp;&nbsp;
					<label for="save_clear_cache">Clear cache when saving a post.</label>
				</p>
				<p>
					<input type="checkbox" id="development_mode" name="development_mode" value="1" <?php if($development_mode == 1){ echo 'checked'; } ?> />
					&nbsp;&nbsp;
					<label for="development_mode">Enable development mode.</label>
				</p>
			
				<div id="development_mode_text">
					<label><strong>Users with restricted access</strong></label>
					<p>
					<?php
					$users = get_users();
					//print_r($users);
					foreach($users as $u): ?>
							<input type="checkbox" name="aka_restricted_users[<?php echo $u->ID ?>]" id="restricted_user<?php echo $u->ID ?>"
							<?php if(@in_array($u->ID, $restricted_users)):?>
								checked = "checked"
							<?php endif; ?>
							/>
							<label for="restricted_user<?php echo $u->ID ?>"><?php echo $u->user_nicename ?></label>&nbsp;&nbsp;&nbsp;
					<?php endforeach; ?>
					</p>
					<label><strong>Warning message</strong></label>
					<br>
					<textarea name="development_mode_text" cols="60" rows="8"><?php
						if($development_mode_text == "") {
							echo $default_text;
						} else {
							echo $development_mode_text;
						}
					?></textarea>
				</div>
			
				<p>
					<input type="checkbox" id="update_notifications" name="update_notifications" value="1" <?php if($update_notifications == 1){ echo 'checked'; } ?> />
					&nbsp;&nbsp;
					<label for="update_notifications">Enable update notifications.</label>
				</p>
			
				<div id="update_notifications_options">
					<label><strong>Users to monitor</strong></label>
					<p>
					<?php
					$users = get_users();
					//print_r($users);
					foreach($users as $u): ?>
							<input type="checkbox" name="update_notification_users[<?php echo $u->ID ?>]" id="update_notification_user<?php echo $u->ID ?>"
							<?php if(@in_array($u->ID, $update_notification_users)):?>
								checked = "checked"
							<?php endif; ?>
							/>
							<label for="update_notification_user<?php echo $u->ID ?>"><?php echo $u->user_nicename ?></label>&nbsp;&nbsp;&nbsp;
					<?php endforeach; ?>
					</p>
					<label><strong>Recipient emails</strong> (one email address per line)</label>
					<br>
					<textarea name="update_notification_recipients" cols="60" rows="8"><?php echo $update_notification_recipients;?></textarea>
				</div>
			
			
			
			
				<p class="submit">
					<input type="submit" name="submit" value="Save Changes" class="button-primary" />
				</p>
			</form>
		</div>
		<script type="text/javascript">
		jQuery(document).ready(function() {
			if(jQuery("#development_mode").is(':checked')) {
				jQuery('#development_mode_text').show();
			}
			jQuery("#development_mode").click( function(){
			   if( jQuery(this).is(':checked') ) {
			   		jQuery('#development_mode_text').slideDown();
			   } else {
			   		jQuery('#development_mode_text').slideUp();
			   }
			});
		
			if(jQuery("#update_notifications").is(':checked')) {
				jQuery('#update_notifications_options').show();
			}
			jQuery("#update_notifications").click( function(){
			   if( jQuery(this).is(':checked') ) {
			   		jQuery('#update_notifications_options').slideDown();
			   } else {
			   		jQuery('#update_notifications_options').slideUp();
			   }
			});
		
		});
		</script>
		
		<?php

	}
	
	public function admin_js() {
		if((get_option('development_mode') == 1 || ( isset( $_POST['development_mode'] ) && $_POST['development_mode'] == 1))) {
    		if(isset($_POST['development_mode_text']) && !isset($_POST['development_mode'])) {
    			return false;
    		}
    		global $user_ID;
    		$restricted_users = get_option('aka_restricted_users');
    		?>
    		<script type="text/javascript">
    		jQuery(document).ready(function() {
    			var dev_text = "<?php if(get_option('development_mode_text') !== '') {echo esc_js(get_option('development_mode_text'));} else { echo esc_js($_POST['development_mode_text']);} ?>";
    			<?php
    			if(isset($_POST['development_mode_text']) && !empty($_POST['development_mode_text'])) {
    				?>
    				dev_text = "<?php echo esc_js($_POST['development_mode_text']); ?>";
    				<?php
    			}
    			if(in_array($user_ID, $restricted_users)){
    				?>
    				jQuery('#wpwrap').append('<div class="restrictedOverlay"></div>');
    				<?php
    			}
    			?>
    			jQuery('#wpwrap').prepend('<div id="development_mode_banner">' + dev_text + '</div>');
		
    		});
    		</script>
    		<?php
    	}
	}
	
	public function process_images($results) {
		if(get_option('image_compressing') == 1) {
			require(AKASETTINGS_PLUGIN_DIR.'/library/php/simpleimage.php');
	   		if(preg_match("@image(.*)@", $results['type'])) {
	   			if(filesize($results['file']) >= 500000) {
	   				$size = getimagesize($results['file']);
	   				$image = new SimpleImage();
	   				$image->load($results['file']);
	   				$image->resizeToWidth($size[0]);
	   				$image->save($results['file']);
	   			}
	   		}
	   	}
	    return $results;
	}
	
	public function save_clear_cache() {
	    global $post;
	    // verify if this is an auto save routine.
	    // If it is our form has not been submitted, so we dont want to do anything
	    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
	    return;
	    if(get_option('save_clear_cache') == 1) {
	    	if (function_exists('w3tc_pgcache_flush')) {
				w3tc_pgcache_flush();
	    		w3tc_dbcache_flush();
	    		w3tc_objectcache_flush();
	    		w3tc_minify_flush();
	    	}
	    }
	}
	
	public function custom_admin_strip_stupid_unicode($data, $rawdata) {
	    if (get_option('strip_stupid_unicode')) {
	        // Strip Unicode nonbreaking space (00a0), zero-width space (200b) and
	        // ETX (03) characters
	        $data['post_content'] = preg_replace('/[\x{00a0}\x{200b}\x{03}]/u', ' ', $data['post_content']);
	    }
	    return $data;
	}
	
	public function save_and_notify($post_id) {
	    global $user_ID, $user_identity;
	
	    $postdata = get_post($post_id);
	
	    // this is an autosave - we don't want to send notifications
	    if ( wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)){
	    	return;
	    }
	
	    //avoid getting notifications for WP creating 'auto_draft' posts when you click New Post.
	    if($postdata->post_status != 'publish'){
	    	return;
	    }
	
	    $update_notifications = get_option('aka_update_notifications');
	    $update_notification_users = get_option('aka_update_notification_users');
	    $update_notification_recipients = get_option('aka_update_notification_recipients');
	
	    // not monitoring at all - exit
	    if(!$update_notifications){
	    	return;
	    }
	
	    // not monitoring the current user - exit gracefully
	    if(!in_array($user_ID, $update_notification_users)){
	    	return;
	    }
	
	    // URL to the edit post page
	    $post_link = admin_url("post.php?post=$post_id&action=edit");
		
	    $article_title = $postdata->post_title;
	    $article_excerpt = $postdata->post_content;;
		
	    $site_name = get_bloginfo();
	    $subject 	=  "[Site update - $site_name] ".$article_title;
	
	    $recipients = explode("\n",$update_notification_recipients);
	    $message = 'The article "'.$article_title.'" on the site, "'.$site_name.'" was just updated by  '.$user_identity.'.';
	    $message .= "\n\n";
	    $message .= 'To view or edit the post go to  '.$post_link;
	    $message .= "\n\n";
		
	    foreach($recipients as $email){
			wp_mail($email, $subject, $message);
	    }
	
	}
		
	
}
