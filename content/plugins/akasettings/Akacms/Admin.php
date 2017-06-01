<?php
/**
* AKA CMS admin page class
*/
class Akacms_Admin {

    public static
    $instance = null;

    /**
    * Object constructor
    */
    function __construct() {
        self::$instance = $this;

		// Add submenu and page
		add_action('admin_menu', array($this, 'admin_menu'));
    }

	public function admin_menu() {
		add_submenu_page('akasettings_general', 'AKA Settings > General', 'General', 'manage_options', 'akasettings_general', array($this, 'aka_cms_admin_page'));
	}

	public function aka_cms_admin_page() {
		if(isset($_POST['submit'])){
			$sanitised = filter_input_array(INPUT_POST,
			    array(
			    	'book_url' => FILTER_SANITIZE_STRING
			    )
			);
			update_option('book_url',$sanitised['book_url']);
			update_option('h1_value', filter_var($_POST['h1_value'], FILTER_SANITIZE_STRING));

			$h1_exc_url = $_POST['h1_exc_url'];
			$h1_exc_value = $_POST['h1_exc_value'];
			$h1_exceptions = array();
			$counter = 0;
			if(!empty($h1_exc_url)) {
				for($i = 0 ; $i < sizeof($h1_exc_url) ; $i++) {
					$h1_exc_url[$i] = Trim($h1_exc_url[$i]);
					if(!empty($h1_exc_url[$i]) && !empty($h1_exc_value[$i])) {
						$h1_exceptions[$counter]['url'] = $h1_exc_url[$i];
						$h1_exceptions[$counter]['value'] = filter_var($h1_exc_value[$i], FILTER_SANITIZE_STRING);
						$counter++;
					}
				}
			}
			update_option('h1_exceptions', json_encode($h1_exceptions));
		}

		$book_url = get_option('book_url');
		$h1_value = get_option('h1_value');
		$h1_exceptions = json_decode(get_option('h1_exceptions'));

		?>
		<script>
			var $ = jQuery;
			$(document).ready(function() {
				$('body').on('click', '.h1_exception .button-secondary', function(ev) {
					$(this).parents('.h1_exception').after('<p class="h1_exception"><input name="h1_exc_url[]" type="text" placeholder="URL, Ex: /about/"><input style="min-width:300px;" name="h1_exc_value[]" type="text" placeholder="VALUE, just as above"><a href="#" class="button-secondary">+</a></p>');
					$(this).remove();
				});
			});
		</script>

		<div class="wrap">
			<form method="post" action="admin.php?page=akasettings_general">

				<h2>General AKA Settings</h2>

				<h3>Options</h3>

				<table class="form-table">
					<tbody>
					    <tr valign="top">
					    	<th scope="row"><label for="book_url">Book Tickets Url</label></th>
					    	<td><input name="book_url" id="book_url" type="text" value="<?php echo $book_url; ?>"></td>
					    </tr>
					    <tr valign="top">
					    	<th valign="top" scope="row"><label for="h1_value">H1 Value<br /><small style="font-weight:normal;font-style:italic;">You can use %page_title% to display the page title value</small></label></th>
					    	<td valign="top"><p><input style="min-width:300px;" name="h1_value" id="h1_value" type="text" value="<?php echo stripslashes($h1_value); ?>"></p></td>
					    </tr>
					    <tr valign="top">
					    	<th valign="top" scope="row"><label>H1 Exceptions</label></th>
					    	<td valign="top">
					    		<?php
					    		if(!empty($h1_exceptions)) {
					    			$counter = 1;
					    			foreach($h1_exceptions as $h1_exception) {
					    				?>
					    					<p class="h1_exception"><input name="h1_exc_url[]" type="text" placeholder="URL, Ex: /about/" value="<?php echo $h1_exception->url; ?>"><input style="min-width:300px;" name="h1_exc_value[]" type="text" placeholder="VALUE, just as above" value="<?php echo stripslashes($h1_exception->value); ?>"><?php if($counter == sizeof($h1_exceptions)) { ?><a href="#" class="button-secondary">+</a><?php } ?></p>
					    				<?php
					    				$counter++;
					    			}
					    		} else {
					    			?>
					    				<p class="h1_exception"><input name="h1_exc_url[]" type="text" placeholder="URL, Ex: /about/"><input style="min-width:300px;" name="h1_exc_value[]" type="text" placeholder="VALUE, just as above"><a href="#" class="button-secondary">+</a></p>
					    			<?php
					    		}
					    		?>
					    	</td>
					    </tr>
					</tbody>
				</table>


				<p class="submit">
					<input type="submit" name="submit" value="Save Changes" class="button-primary" />
				</p>

			</form>

			<hr>

			<h3>Shortcodes</h3>

			<p>You can use the below shortcodes in any piece of Wordpress content or in the theme using the <a href="http://codex.wordpress.org/Function_Reference/do_shortcode" target="_blank">do_shortcode()</a> function in PHP.</p>
			<p>Example: <em><?php echo htmlentities("<?php echo do_shortcode('[akabooktickets]'); ?>"); ?></em></p>
			<ul>
				<li><code>[akabooktickets]</code> <em>to display the book tickets link. Parameters are button (1 or 0), text (for the anchor tag label) and class.</em></li>
				<li><code>[h1_value]</code> <em>to display right h1 text value.</em></li>
				<li><code>[template]</code> <em>to display the URL of the theme.</em></li>
				<li><code>[blogpath]</code> <em>to display the URL of the site.</em></li>
				<li><code>[akaJsEmail]</code> <em>to display an email address in Javascript (avoid spam). Parameters are email and nolink (0 or 1).</em></li>
				<li><code>[aka_share]</code> <em>to display a sharing link in an anchor tag. Parameters are network (facebook, googleplus, twitter, pinterest, tumblr or email), url (current url if empty), description (only for twitter, tumblr, pinterest and email), title (only for email) and classes.<br><strong>Example:</strong> <code>[aka_share network="facebook" classes="btn btn-share"]Share on Facebook[/aka_share]</code></em></li>
                <li><code>[aka_iframe]</code> <em>to display a responsive iframe, keeping the same ratio regardless of the screen width. Parameters are url and percentage (default is 56.5)<br><strong>Example:</strong> <code>[aka_iframe url="https://www.youtube.com/embed/sNhhvQGsMEc"]</code></em></li>
			</ul>

			<hr>

			<h3>Functions</h3>

			<p>You can use the below functions anywhere in the theme.</p>

			<ul>
				<li><code>aka_share($network, $url, $description, $title)</code> $url, $description and $title are optional. $url defaults to the current url, description and title to an empty string. The function displays the sharing url for the provided network. $network can be facebook, googleplus, twitter, pinterest, tumblr or email.</li>
			</ul>



		</div>


		<?php

	}

}
