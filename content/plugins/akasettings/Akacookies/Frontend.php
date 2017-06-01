<?php
/**
 * AKA Cookies Frontend class
 */
class Akacookies_Frontend {

    public static
    $instance = null;

    /**
     * Object constructor
     */
    function __construct() {
        self::$instance = $this;
		
        // Sett cookie action
        add_action('init', array($this, 'tracking_cookie_set'));
        
        // Footer output
        add_action('wp_footer', array($this, 'cookies_footer_output'));
        
    } 
    
    public function tracking_cookie_set() {
	session_start();
    	if(isset($_SESSION['cookie_notification_'.get_option('cookie_notification_id')]) && $_SESSION['cookie_notification_'.get_option('cookie_notification_id')] == 1) {
    		setcookie('cookie_notification_'.get_option('cookie_notification_id'), '1', mktime (0, 0, 0, 12, 31, 2037), '/');
    	}
    }
    
    public function cookies_footer_output() {
    	// get post and custom
		global $post;
		
		// If we have to notify the user about the cookies
		$cookie_notification = get_option('cookie_notification');
		$cookie_notification_id = get_option('cookie_notification_page');
		if(($cookie_notification == 1 
			&& $cookie_notification_id != $post->ID)) {
		    $cookie_notification_copy = esc_js(get_option('cookie_notification_copy'));
		    $cookie_notification_page = get_page($cookie_notification_id);
		    $cookie_notification_page_link = get_permalink($cookie_notification_page);
			$cookie_notification_copy = str_replace("{cookie_link}", '<a href="'.$cookie_notification_page_link.'" target="_blank">', $cookie_notification_copy);
			$cookie_notification_copy = str_replace("{/cookie_link}", "</a>", $cookie_notification_copy);
		    $cookie_notification_css = get_option('cookie_notification_css');
			$cookie_notification_js = get_option('cookie_notification_js');
			$cookie_notification_suffix = get_option('cookie_notification_id');
		
			$cookie_notification_cookie_name = "cookie_notified" . ($cookie_notification_suffix ? "_{$cookie_notification_suffix}" : '');
		
		    ?>
		
			<script type="text/javascript">
			(function() {
				$(function() {
					var akaCookie = { get: function(c_name) { var i,x,y,ARRcookies=document.cookie.split(";"); for (i=0;i<ARRcookies.length;i++) { x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("=")); y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1); x=x.replace(/^\s+|\s+$/g,""); if (x==c_name) { return unescape(y); } } }, set: function(c_name,value,exdays,path) { var exdate=new Date(); exdate.setDate(exdate.getDate() + exdays); var c_path = '<? echo esc_js($cookie_notification_cookie_path); ?>'; var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString()) + (c_path ? "; path="+c_path : ""); document.cookie=c_name + "=" + c_value; } };
		
					var viewed = akaCookie.get('<?php echo esc_js($cookie_notification_cookie_name); ?>');
					if (viewed) return;
		
					akaCookie.set('<?php echo esc_js($cookie_notification_cookie_name); ?>', 1, 365*10);
					var $bar = $('<div id="cookie_notification"><?php echo $cookie_notification_copy; ?> <a href="#" class="close">Close</a></div>');
					$('a.close', $bar).click(function(e) {
						e.preventDefault();
						$bar.remove();
					});
					$('body').prepend($bar);
					$('head').append('<style type="text/css">\
						#cookie_notification {\
						width: 100%; z-index: 99999; padding: 7px 0 9px;\
						text-align: center; font-family: Verdana, Arial, Helvetica, sans-serif;  font-size: 12px; color: #272727;\
						background: #f9f9f9; border-bottom: 1px dotted #272727;\
						-webkit-box-shadow: 0px 3px 10px rgba(50, 50, 50, 0.43);\
						-moz-box-shadow: 0px 3px 10px rgba(50, 50, 50, 0.43);\
						box-shadow: 0px 3px 10px rgba(50, 50, 50, 0.43);\
						position: relative;\
						}\
						#cookie_notification a { color: #272727; text-decoration: underline; }\
						<?php echo $cookie_notification_css; ?>
						</style>');
						<?php
						if (strlen(trim($cookie_notification_js)) > 0) {
						?>
						(function() {
							<?php echo $cookie_notification_js; ?>
						}).apply($bar);
					<?php
					}
					?>
				});
			})();
			</script>
			<?php	
		}
    }
        
}
