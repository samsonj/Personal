<?php
/**
* AKA Cookies admin page class
*/
class Akacookies_Admin {

    public static
    $instance = null;

    /**
    * Object constructor
    */
    function __construct() {
        self::$instance = $this;
		
		// Add submenu and page
		add_action('admin_menu', array($this, 'admin_menu'), 12);
		add_action('admin_head', array($this, 'aka_cookies_admin_js'));
		add_action('wp_ajax_generate_cookie_table', array($this, 'generate_cookie_table'));
		add_action('generate_cookie_table_hook', 'generate_cookie_table');
    }
	
	public function admin_menu() {
		add_submenu_page('akasettings_general', 'AKA Settings > Cookies', 'Cookies', 'manage_options', 'akasettings_cookies', array($this, 'aka_cookies_admin_page'));
	}
	
	public function aka_cookies_admin_page() {
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br></div>
			<h2>Cookies AKA Settings</h2>
			<?php
			if(isset($_POST) && !empty($_POST)) {
				update_option('cookie_notification', $_POST['cookie_notification']);
				if(is_numeric($_POST['cookie_notification_page'])) {
					update_option('cookie_notification_page', $_POST['cookie_notification_page']);
				}
				$cookie_site_name = filter_var($_POST['cookie_site_name'], FILTER_SANITIZE_STRING);
				$cookie_site_name = trim($cookie_site_name);
				update_option('cookie_site_name', $cookie_site_name);
				if($cookie_site_name) {
					// Schedule the cron
					if (!wp_next_scheduled('generate_cookie_table_hook')) {
						wp_schedule_event(time(), 'twicedaily', 'generate_cookie_table_hook');
					}
				} else {
					wp_clear_scheduled_hook('generate_cookie_table_hook');
				}
				$cookie_notification_id = filter_var($_POST['cookie_notification_id'], FILTER_SANITIZE_STRING);
				$cookie_notification_id = trim($cookie_notification_id);
				update_option('cookie_notification_id', $cookie_notification_id);
				$cookie_notification_copy = filter_var($_POST['cookie_notification_copy'], FILTER_SANITIZE_STRING);
				$cookie_notification_copy = trim($cookie_notification_copy);
				update_option('cookie_notification_copy', $cookie_notification_copy);
				update_option('cookie_notification_css', stripslashes($_POST['cookie_notification_css']));
				update_option('cookie_notification_js', stripslashes($_POST['cookie_notification_js']));
				?>
				<div id="setting-error-settings_updated" class="updated settings-error"> <p>Settings saved.</p></div>
				<?php
			}
			
			$cookie_site_name = get_option('cookie_site_name');
			$cookie_notification = get_option('cookie_notification');
			$cookie_notification_id = get_option('cookie_notification_id');
			$cookie_notification_page = get_option('cookie_notification_page');
			$cookie_notification_copy = get_option('cookie_notification_copy');
			$cookie_notification_css = get_option('cookie_notification_css');
			$cookie_notification_js = get_option('cookie_notification_js');
			
			?>
			
			
			<form method="post" action="admin.php?page=akasettings_cookies">
			<h3>Cookie table</h3>
			<p>Provide a correct cookie site name on the field below, save the settings and generate the cookie table.<br />When this is completed and successful, you can display the cookie table anywhere on the site with the shortcode <code>[cookietable]</code></p>
			<table class="form-table cookies-form-table">
				<tr  valign="top">
					<th scope="row"><label for="cookie_site_name">Cookie site name:</label></th>
					<td><input style="width: 180px;" name="cookie_site_name" type="text" id="cookie_site_name" value="<?php echo $cookie_site_name; ?>" class="regular-text"> <?php if($cookie_site_name){ ?><input class="button-secondary" id="cookieTableBtn" type="button" value="Generate the cookie table"><?php } ?></td>
				</tr>
			</table>
			<h3>Cookie notification</h3>
			<table class="form-table cookies-form-table">
				<tr valign="top">
					<th scope="row"><label for="cookie_notification">Activate cookie notification:</label></th>
					<td><input name="cookie_notification" type="checkbox" id="cookie_notification" value="1" class="regular-checkbox" <?php if($cookie_notification == 1) { echo "checked"; } ?>></td>
				</tr>
				<tr class="cookie_notification_copy_box" valign="top">
					<th scope="row"><label for="cookie_notification_id">Cookie notification ID:</label></th>
					<td><input name="cookie_notification_id" type="text" id="cookie_notification_id" value="<?php echo $cookie_notification_id; ?>" class="regular-text"></td>
				</tr>
				<tr class="cookie_notification_copy_box" valign="top">
					<th scope="row"><label for="cookie_notification_page">Cookie notification page:</label></th>
					<td>
						<select name="cookie_notification_page" id="cookie_notification_page">
							<option value="">-- Select a page</option>
							<?php
							$pages = get_pages();
							foreach($pages as $page) {
								?>
								<option value="<?php echo $page->ID; ?>" <?php if($cookie_notification_page == $page->ID) { echo "selected"; } ?>><?php echo $page->post_title; ?></option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr class="cookie_notification_copy_box" valign="top">
					<th scope="row"><label for="cookie_notification_copy">Cookie notification copy:</label></th>
					<td>
						<textarea name="cookie_notification_copy" id="cookie_notification_copy"><?php echo $cookie_notification_copy; ?></textarea>
						<p><em>You can wrap some part of the text above between {cookie_link} and {/cookie_link} to make it link through the cookie privacy policy page</em></p>
					</td>
				</tr>
				<tr class="cookie_notification_copy_box" valign="top">
					<th scope="row"><label for="cookie_notification_css">Cookie notification CSS:</label></th>
					<td>
						<textarea name="cookie_notification_css" id="cookie_notification_css"><?php echo $cookie_notification_css; ?></textarea>
					</td>
				</tr>
				<tr class="cookie_notification_copy_box" valign="top">
					<th scope="row"><label for="cookie_notification_js">Cookie notification JS:</label></th>
					<td>
						<textarea name="cookie_notification_js" id="cookie_notification_js"><?php echo $cookie_notification_js; ?></textarea>
					</td>
				</tr>
			</table>
			
			<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"></p>
			</form>
		</div>
		<?php
	}
	
	// Cookie notification admin front-end stuff below
	public function aka_cookies_admin_js(){
		?>
		<script type="text/javascript">
			/* <![CDATA[ */
			jQuery(document).ready(function() {
				var $ = jQuery;
				setTimeout(function() { $('#setting-error-settings_updated').fadeOut(500); }, 2000);
				$('.cookie_notification_copy_box').hide();
				if($('#cookie_notification').is(':checked')) {
					$('.cookie_notification_copy_box').show();
				}
				$('#cookie_notification').change(function() {
					if($('#cookie_notification').is(':checked')) {
						$('.cookie_notification_copy_box').fadeIn();
					} else {
						$('.cookie_notification_copy_box').fadeOut();
					}
				})
				
				$('#cookieTableBtn').click(function() {
					$(this).attr('disabled', 'disabled');
					$(this).after("<img src='images/wpspin_light.gif' id='loadingCookieTable'>");
					$.ajax(ajaxurl, {
						type: 'POST',
						data: {
							action: 'generate_cookie_table'
						},
						success: function(data, textStatus) {
							if(data == 1) {
								$('#loadingCookieTable').replaceWith("<span style='color:green;' id='cookie_table_message'>Cookie table successfully generated!</span>");
							} else {
								$('#loadingCookieTable').replaceWith("<span style='color:red;' id='cookie_table_message'>A problem occurred, please check that everything is correct!</span>");
							}	
							$('#cookie_table_message').fadeOut(2000);					
							$('#cookieTableBtn').removeAttr('disabled');
						}
					});
				});
			}); 
			/* ]]> */
		</script>
		<?php
	}
	
	public function generate_cookie_table() {
		global $wpdb; // this is how you get access to the database

		$cookie_site_name = get_option('cookie_site_name');
		$cookie_table = trim(file_get_contents('http://digital.akauk.com/cookies/site/'.$cookie_site_name));
		if(!empty($cookie_table) && strpos($cookie_table, '<table class="cookieTable">') !== false) {
			update_option('cookie_table', $cookie_table);
			echo 1;
		} else {
			update_option('cookie_table', '');
			echo 0;
		}
			
		die(1);
	}
	
	
	
}