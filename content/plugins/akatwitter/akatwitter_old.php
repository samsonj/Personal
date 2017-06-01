<?php

include(dirname(__FILE__) . "/twitteroauth/twitteroauth.php");

class akaTwitter extends WP_Widget {
	/** constructor */
	function __construct(){
		parent::__construct('akatwitter', 'AKA Twitter');
		wp_enqueue_script("twitter-widgets", '//platform.twitter.com/widgets.js', null, 1, true);
    }

	static function normalize_args($inst) {
		$instance = array();
		foreach ($inst as  $k => $v) {
			$nk = str_replace('akatwitter_', '', $k);
			@$instance[$nk] = $v;
		}
		return $instance;
	}

	// main function
	static function akaTwitter($instance) {
		$data = '<div class="'.$instance['classes'].'">';

		if(!empty($instance['user'])){
			$twitterUser = $instance['user']; // user
		}

		if ($instance['exclude_replies']) {
			$exclude_replies = true;
		}

		if ($instance['faves']) {
			$twitterFaves = true;
		}

		if(!empty($instance['hash'])){
			$twitterHash = $instance['hash']; // hash
		}

		if(!empty($instance['tweets'])){
			$twitterTweets = $instance['tweets']; // number of tweets
		} else {
			$twitterTweets = 3;
		}

		$twitterText = stripslashes($instance['text']); // number of tweets
		$showUser = $instance['showuser'];
		$showPic = $instance['showimage'];

		if(!empty($twitterUser) && !empty($twitterHash) ){
			$twitterType = 'usersHash';
		}
		else if (!empty($twitterUser)){
			$twitterType = 'user';
			if ($twitterFaves) {
				$twitterType = 'faves';
			}
		}
		else if($twitterHash){
			$twitterType = 'hash';
		}

		// check if a twitter user has been setup
		if((!empty($twitterUser)) || (!empty($twitterHash))){
			// Check for our cache directory directory, and create it if it doesn't exists
			$dname = WP_CONTENT_DIR . "/cache/akatwitter";
			if(!file_exists($dname))
				mkdir($dname, 0755, true);

			$fname = $twitterType.'-'.(($twitterType=='user'||$twitterType=='faves')?$twitterUser:preg_replace("/[^a-zA-Z0-9_-]/", '', $twitterHash));
			$newfile = WP_CONTENT_DIR . "/cache/akatwitter/" . $fname . "-new.json";
			$file    = WP_CONTENT_DIR . "/cache/akatwitter/" . $fname . ".json";

			if (!file_exists($file) || filemtime($file) < $LastModPlusFiveMinutes = strtotime("-5 minutes")) {
				$twitter_api = new TwitterOAuth(
					get_option('akatwitter_consumer_key'),
					get_option('akatwitter_consumer_secret'),
					get_option('akatwitter_oauth_token'),
					get_option('akatwitter_oauth_token_secret')
				);

				$tweets = aka_twitter_get($type, $params = array());

				switch ($twitterType) {
					case 'usersHash':
						//$tweets = $twitter_api->get('search/tweets', array('q' => 'from:'. $twitterUser .' AND '. $twitterHash ,'count' => $twitterTweets));
						//if (!$tweets->errors) {
						//	$tweets = $tweets->statuses;
						//}

						$parameters = array('screen_name' => $twitterUser);
						if($exclude_replies == true) {
							$parameters['exclude_replies'] = true;
						} else {
							$parameters['count'] = $twitterTweets;
						}
						$tweets = $twitter_api->get('statuses/user_timeline', $parameters);
						$tweets_to_keep = array();
						$twitterTweets = count($tweets);

						$twitterHashs = explode(' ', $twitterHash);

						foreach($twitterHashs as $twitterHashtag) {
							for($x=0;$x<$twitterTweets;$x++) {
							   if (strpos($tweets[$x]->text , $twitterHashtag ) !== false){
							   		    $tweets_to_keep[] = $tweets[$x];
							   			break;
							   }
							}
						}
						$tweets = $tweets_to_keep;
					break;
					case 'user':
						$parameters = array('screen_name' => $twitterUser);
						if($exclude_replies == true) {
							$parameters['exclude_replies'] = true;
						} else {
							$parameters['count'] = $twitterTweets;
						}
						$tweets = $twitter_api->get('statuses/user_timeline', $parameters);

						break;
					case 'faves':
						$tweets = $twitter_api->get('favorites/list', array('screen_name' => $twitterUser, 'count' => $twitterTweets));
						break;
					case 'hash':
						$tweets = $twitter_api->get('search/tweets', array('q' => $twitterHash, 'count' => $twitterTweets));
						if (!$tweets->errors) {
							$tweets = $tweets->statuses;
						}

						break;
				}

				$success = true;
				// Don't save the error, save an empty list of tweets
				if (!$tweets->errors) {
					file_put_contents($file, json_encode($tweets));
					$success = true;
				} else {
					file_put_contents($file, json_encode(array()));
					$success = false;
				}
				do_action('twitter_updated', $success);

			} else {
				$tweets = json_decode(file_get_contents($file));
			}

			/* Begin output if we have options and tweets to show */

			// show pic
			if( (!empty($showPic)) && ($tweets[0]->profile_image_url != null) ){
				$data .= '<img src="'.$tweets[0]->profile_image_url.'" alt="'.$twitterUser.'" class="twitterImage" />';
			}

			// show username
			if(!empty($showUser)){
				$data .= '<span class="twitterUser">'.$twitterUser.'</span>';
			}

			// show some text
			if(!empty($twitterText)){
				$data .= $twitterText;
			}

			// check if there are tweets
			if(is_array($tweets) && count($tweets) > 0){

				$data .= '<ul class="tweets">';
					// loop through tweets till we reach our limit
					if(count($tweets) > $twitterTweets){
						$twitterTweets = $twitterTweets;
					} else {
						$twitterTweets = count($tweets);
					}

					for( $x = 0 ; $x < $twitterTweets ; $x++ ) {
						$str = "<p>".akaTwitter::twitter_links($tweets[$x]->text)."</p>";
        				// The below is only to ignore emoji icons
        				$str2 = iconv("UTF-8", "ASCII//IGNORE", $str);
        				if($str2) {
        					$str = $str2;
        				}

						// changing the text if there is a character limit
						if($instance['characterlimit']>0) {
							$str = substr(strip_tags($str),0,$instance['characterlimit'])."...";
						}

						if (!empty($instance['showuserlink'])) {
							$str .= '<ul><li class="user"><a href="http://twitter.com/'. $tweets[$x]->user->screen_name .'" target="_blank">'.$tweets[$x]->user->name.'</a></li></ul>';

						}

						if(!empty($instance['showbuttons'])){
							// create buttons
							$btns .= '<ul class="tweetBtns">';


								$retweet_url = 'http://twitter.com/intent/retweet?tweet_id='.$tweets[$x]->id_str;
								$reply_url = 'http://twitter.com/intent/tweet?in_reply_to='.$tweets[$x]->id_str;

								if ($instance['showreplyretweet']) {
									// retweet url
									$btns .= '<li class="retweet"><a href="'.$retweet_url.'" target="_blank">Retweet</a></li>';

									// reply url
									$btns .= '<li class="reply"><a href="'.$reply_url.'" target="_blank">Reply</a></li>';
								}

								// get how long ago
								$now = date('U');
								$posted = strtotime($tweets[$x]->created_at);
								$since_post = date('U', $now - $posted);
								if($since_post < 60){
									if($since_post == 1){
										$btns .= '<li class="sent">'.$since_post.' second ago</li>';
									} else {
										$btns .= '<li class="sent">'.$since_post.' seconds ago</li>';
									}
								} else if($since_post < 3600){
									if(date('i', $since_post) == 1){
										$btns .= '<li class="sent">'.date('i', $since_post).' minute ago</li>';
									} else {
										$btns .= '<li class="sent">'.date('i', $since_post).' minutes ago</li>';
									}
								} else if($since_post < 86400){
									if(date('G', $since_post) == 1){
										$btns .= '<li class="sent">'.date('G', $since_post).' hour ago</li>';
									} else {
										$btns .= '<li class="sent">'.date('G', $since_post).' hours ago</li>';
									}
								} else if($since_post < 604800){
									if(date('z', $since_post) == 1){
										$btns .= '<li class="sent">'.date('z', $since_post).' day ago</li>';
									} else {
										$btns .= '<li class="sent">'.date('z', $since_post).' days ago</li>';
									}
								} else {
									if(round($since_post/604800,0,PHP_ROUND_HALF_DOWN) == 1){
										$btns .= '<li class="sent">'.round($since_post/604800,0,PHP_ROUND_HALF_DOWN).' week ago</li>';
									} else {
										$btns .= '<li class="sent">'.round($since_post/604800,0,PHP_ROUND_HALF_DOWN).' weeks ago</li>';
									}
								}
								if ($instance['showvia']) {
									// source
									$btns .= '<li class="via">via '.html_entity_decode($tweets[$x]->source).'</li>';
								}

							$btns .= '</ul>';

							$str .= $btns; // add buttons
							unset($btns);
						}

						if($str != ''){
							$data .= '<li class="tweet">';
							// add text before tweet
							if($instance['beforeli']!="") {
								$data .= $instance['beforeli'];
							}
							$data .= $str;
							// add text after tweet
							if($instance['afterli']!="") {
								$data .= $instance['afterli'];
							}
							$data .= "</li>\n";
						}
					}
				$data .= "</ul>";

			} else {
				$data .= '<p>Sorry, tweets are not available at the moment.  Hopefully they will be back up soon</p>';
			}
		} else {
			$data .= '<p>Sorry, no twitter user has been selected</p>';
		}

		if(!empty($instance['follow'])){

			// add javascript
			add_action('wp_footer', function(){
				wp_register_script('twitterFollow', 'http://platform.twitter.com/widgets.js', array(), '1.0', true);
				wp_print_scripts('twitterFollow');
			});

			$data .= '<div id="followBtn">';
				$data .= '<a class="twitter-follow-button" href="http://twitter.com/'.$twitterUser.'" data-button="grey" data-text-color="#FFFFFF" data-link-color="#00AEFF">';
					$data .= 'Follow @'.$twitterUser;
				$data .= '</a>';
			$data .= '</div>';
		}

		$data .= '</div>';

		return $data;
	}

	// ready twitter feed
	static function twitter_links($text){
	    // convert URLs into links
	    $text = preg_replace("#(https?://([-a-z0-9]+\.)+[a-z]{2,5}([/?][-a-z0-9!\#()/?&+]*)?)#i", "<a href='$1' target='_blank'>$1</a>",$text);
	    // convert protocol-less URLs into links
	    $text = preg_replace("#(?!https?://|<a[^>]+>)(^|\s)(([-a-z0-9]+\.)+[a-z]{2,5}([/?][-a-z0-9!\#()/?&+.]*)?)\b#i", "$1<a href='http://$2'>$2</a>",$text);

	    // convert @mentions into follow links
	    $text = preg_replace("#(?!https?://|<a[^>]+>)(^|\s)(@([_a-z0-9\-]+))#i", "$1<a href=\"http://twitter.com/$3\" title=\"Follow $3\" target=\"_blank\">@$3</a>",$text);
	    // convert #hashtags into tag search links
	    $text = preg_replace("#(?!https?://|<a[^>]+>)(^|\s)(\#([_a-z0-9\-]+))#i", "$1<a href='http://twitter.com/search?q=%23$3' title='Search tag: $3' target='_blank'>#$3</a>",$text);

	    return $text;
	}

	/** @see WP_Widget::widget */
	function widget( $args, $inst ) {
		$instance = self::normalize_args($inst);
		extract($args);
		$data = akaTwitter::akaTwitter($instance);

		if(!empty($instance['showtitle'])){
			$output = $before_widget.$before_title.$instance['title'].$after_title.$data.$after_widget;
		} else {
			$output = $before_widget.$data.$after_widget;
		}
		echo $output;
	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['akatwitter_showtitle']        = strip_tags($new_instance['akatwitter_showtitle']);
		$instance['akatwitter_title']            = strip_tags($new_instance['akatwitter_title']);
		$instance['akatwitter_classes']          = strip_tags($new_instance['akatwitter_classes']);
		$instance['akatwitter_text']             = $new_instance['akatwitter_text'];
		$instance['akatwitter_showuser']         = strip_tags($new_instance['akatwitter_showuser']);
		$instance['akatwitter_showimage']        = strip_tags($new_instance['akatwitter_showimage']);
		$instance['akatwitter_user']             = strip_tags($new_instance['akatwitter_user']);
		$instance['akatwitter_exclude_replies']  = strip_tags($new_instance['akatwitter_exclude_replies']);
		$instance['akatwitter_faves']            = strip_tags($new_instance['akatwitter_faves']);
		$instance['akatwitter_hash']             = strip_tags($new_instance['akatwitter_hash']);
		$instance['akatwitter_tweets']           = strip_tags($new_instance['akatwitter_tweets']);
		$instance['akatwitter_characterlimit']   = strip_tags($new_instance['akatwitter_characterlimit']);
		$instance['akatwitter_beforeli']         = $new_instance['akatwitter_beforeli'];
		$instance['akatwitter_afterli']          = $new_instance['akatwitter_afterli'];
		$instance['akatwitter_follow']           = strip_tags($new_instance['akatwitter_follow']);
		$instance['akatwitter_showbuttons']      = strip_tags($new_instance['akatwitter_showbuttons']);
		$instance['akatwitter_showreplyretweet'] = strip_tags($new_instance['akatwitter_showreplyretweet']);
		$instance['akatwitter_showuserlink']     = strip_tags($new_instance['akatwitter_showuserlink']);
		$instance['akatwitter_showvia']          = strip_tags($new_instance['akatwitter_showvia']);

		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		if (!$instance['akatwitter_tweets']) $instance['akatwitter_tweets'] = 3;
	?>
		<p>
			<label><input type="checkbox" name="<?php echo $this->get_field_name('akatwitter_showtitle'); ?>" value="1" class="checkbox" <?php if($instance['akatwitter_showtitle'] == 1){ echo 'checked=checked'; } ?> /> <?php _e("Show Title: " ); ?></label>
			<br />
		</p>
		<p>
			<label><?php _e("Widget Title: " ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('akatwitter_title'); ?>" value="<?php echo $instance['akatwitter_title']; ?>" size="20" class="widefat" />
			<br />
			<?php _e(" ex: Twitter Feed" ); ?>
			<br />
		</p>
		<p>
			<label><?php _e("Widget Classes: " ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('akatwitter_classes'); ?>" value="<?php echo $instance['akatwitter_classes']; ?>" size="20" class="widefat" />
			<br />
			<?php _e(" ex: class1 class2" ); ?>
			<br />
		</p>
		<p>
			<label><?php _e("Widget Text: " ); ?></label>
			<textarea name="<?php echo $this->get_field_name('akatwitter_text'); ?>" rows="10" cols="20" class="widefat"><?php echo $instance['akatwitter_text']; ?></textarea>
			<br />
			<?php _e(" ex: Lorem ipsum dolor..." ); ?>
			<br />
		</p>
		<p>
			<label><?php _e("Show Username: " ); ?></label>
			<select name="<?php echo $this->get_field_name('akatwitter_showuser'); ?>">
				<option value="1" <?php if($instance['akatwitter_showuser'] == 1) echo "selected=selected"; ?>>Yes</option>
				<option value="0" <?php if($instance['akatwitter_showuser'] == 0) echo "selected=selected"; ?>>No</option>
			</select>
			<br />
		</p>
		<p>
			<label><?php _e("Show Avatar: " ); ?></label>
			<select name="<?php echo $this->get_field_name('akatwitter_showimage'); ?>">
				<option value="1" <?php if($instance['akatwitter_showimage'] == 1) echo "selected=selected"; ?>>Yes</option>
				<option value="0" <?php if($instance['akatwitter_showimage'] == 0) echo "selected=selected"; ?>>No</option>
			</select>
			<br />
		</p>
		<p>
			<label><?php _e("Twitter Username: " ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('akatwitter_user'); ?>" value="<?php echo $instance['akatwitter_user']; ?>" size="20" class="widefat" />
			<br />
			<?php _e(" ex: gazzwi86" ); ?>
			<br />
		</p>
		<p>
			<label><input type="checkbox" name="<?php echo $this->get_field_name('akatwitter_exclude_replies'); ?>" value="1" class="checkbox" <?php if($instance['akatwitter_exclude_replies'] == 1){ echo 'checked=checked'; } ?> /> <?php _e("Exclude replies (if Twitter Username)" ); ?></label>
			<br />
		</p>
		<p>
			<label><input type="checkbox" name="<?php echo $this->get_field_name('akatwitter_faves'); ?>" value="1" class="checkbox" <?php if($instance['akatwitter_faves'] == 1){ echo 'checked=checked'; } ?> /> <?php _e("Show user's favorites" ); ?></label>
			<br />
		</p>
		<p>
			<label><?php _e("Twitter Hashtag: " ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('akatwitter_hash'); ?>" value="<?php echo $instance['akatwitter_hash']; ?>" size="20" class="widefat" />
			<br />
			<?php _e(" ex: oliviers" ); ?>
			<br />
		</p>
		<p>
			<label><?php _e("Tweets: " ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('akatwitter_tweets'); ?>" value="<?php echo $instance['akatwitter_tweets']; ?>" size="20" class="widefat" />
			<br />
			<?php _e(" ex: 1 (min)" ); ?>
			<br />
		</p>
		<p>
			<label><?php _e("Character Limit: " ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('akatwitter_characterlimit'); ?>" value="<?php echo $instance['akatwitter_characterlimit']; ?>" size="20" class="widefat" />
			<br />
			<?php _e(" ex: 100" ); ?>
			<br />
		</p>
		<p>
			<label><?php _e("Before li: " ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('akatwitter_beforeli'); ?>" value="<?php echo $instance['akatwitter_beforeli']; ?>" size="20" class="widefat" />
			<br />
			<?php _e(" ex: Latest Tweet" ); ?>
			<br />
		</p>
		<p>
			<label><?php _e("After li: " ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('akatwitter_afterli'); ?>" value="<?php echo $instance['akatwitter_afterli']; ?>" size="20" class="widefat" />
			<br />
			<?php _e(" ex: Latest Tweet" ); ?>
			<br />
		</p>
		<p>
			<label><input type="checkbox" name="<?php echo $this->get_field_name('akatwitter_showuserlink'); ?>" value="1" class="checkbox" <?php if($instance['akatwitter_showuserlink'] == 1){ echo 'checked'; } ?> /> <?php _e("Show link to user" ); ?></label>
			<br />
		</p>
		<p>
			<label><input type="checkbox" name="<?php echo $this->get_field_name('akatwitter_showbuttons'); ?>" value="1" class="checkbox" <?php if($instance['akatwitter_showbuttons'] == 1){ echo 'checked'; } ?> /> <?php _e("Show time and buttons" ); ?></label>
			<br />
		</p>
		<p>
			<label><input type="checkbox" name="<?php echo $this->get_field_name('akatwitter_showreplyretweet'); ?>" value="1" class="checkbox" <?php if($instance['akatwitter_showreplyretweet'] == 1){ echo 'checked'; } ?> /> <?php _e("Show reply and retweet buttons" ); ?></label>
			<br />
		</p>
		<p>
			<label><input type="checkbox" name="<?php echo $this->get_field_name('akatwitter_showvia'); ?>" value="1" class="checkbox" <?php if($instance['akatwitter_showvia'] == 1){ echo 'checked'; } ?> /> <?php _e("Show tweet source ('via')" ); ?></label>
			<br />
		</p>
		<p>
			<label><input type="checkbox" name="<?php echo $this->get_field_name('akatwitter_follow'); ?>" value="1" class="checkbox" <?php if($instance['akatwitter_follow'] == 1){ echo 'checked'; } ?> /> <?php _e("Follow button: " ); ?></label>
			<br />
		</p>
		<?php
	}

}

add_action('widgets_init', 'aka_twitter_widgets_init');

function aka_twitter_widgets_init() { return register_widget('akaTwitter'); }

/* SHORTCODE */
/*
	[akaTwitter
		showtitle="0"
		title="akaTwitter"
		text=""
		showUser=""
		showImage=""
		user="akaconnect"
		hash=""
		tweets="5"
		follow="1"
		showbuttons="1"
		classes="classy"]
*/
function akatwitter_short($atts){
	$natts = akaTwitter::normalize_args($atts);
	extract( shortcode_atts( array(
		'showtitle'			=> '0',
		'title'				=> 'akaTwitter',
		'text'				=> '',
		'showUser'			=> '0',
		'showImage'			=> '0',
		'user'				=> 'akaconnect',
		'exclude_replies'	=> '0',
		'faves'				=> '0',
		'hash'				=> '',
		'tweets'			=> '5',
		'follow'			=> '0',
		'showbuttons'		=> '0',
		'classes'			=> ''
	), $natts));
	return akaTwitter::akaTwitter($natts);
}
add_shortcode('akaTwitter', 'akatwitter_short');
add_shortcode('akatwitter', 'akatwitter_short');

/**
 * Add options on general settings page for Twitter oAuth
 */
add_action('admin_menu', 'akatwitter_admin_menu');
function akatwitter_admin_menu() {
	add_options_page( 'Twitter Settings', 'Twitter', 'manage_options', 'twitter-settings', 'akatwitter_settings');
}
function akatwitter_settings() {

	if(isset($_POST) && !empty($_POST)) {
		$akatwitter_consumer_key = update_option('akatwitter_consumer_key', filter_var($_POST['akatwitter_consumer_key'], FILTER_SANITIZE_STRING));
		$akatwitter_consumer_secret = update_option('akatwitter_consumer_secret', filter_var($_POST['akatwitter_consumer_secret'], FILTER_SANITIZE_STRING));
		$akatwitter_oauth_token = update_option('akatwitter_oauth_token', filter_var($_POST['akatwitter_oauth_token'], FILTER_SANITIZE_STRING));
		$akatwitter_oauth_token_secret = update_option('akatwitter_oauth_token_secret', filter_var($_POST['akatwitter_oauth_token_secret'], FILTER_SANITIZE_STRING));
	}

	$akatwitter_consumer_key = get_option('akatwitter_consumer_key', true);
	$akatwitter_consumer_secret = get_option('akatwitter_consumer_secret', true);
	$akatwitter_oauth_token = get_option('akatwitter_oauth_token', true);
	$akatwitter_oauth_token_secret = get_option('akatwitter_oauth_token_secret', true);

	?>
	<div class="wrap">
		<h2>Twitter Settings</h2>
		<p>Fill up the form below with the Twitter app details after you've created it on the <a href="https://apps.twitter.com/app/new" target="_blank">Twitter Developer Center</a></p>
		<form method="POST" action="options-general.php?page=twitter-settings">
			<table class="form-table">
			    <tr>
			    	<th scope="row">
			    	<label for="akatwitter_consumer_key">API key</label>
			    	</th>
			    	<td>
			    	<input type="text" id="akatwitter_consumer_key" name="akatwitter_consumer_key" value="<?php echo $akatwitter_consumer_key; ?>">
			    	</td>
			    </tr>
			    <tr>
			    	<th scope="row">
			    	<label for="akatwitter_consumer_secret">API secret</label>
			    	</th>
			    	<td><input type="text" id="akatwitter_consumer_secret" name="akatwitter_consumer_secret" value="<?php echo $akatwitter_consumer_secret; ?>"></td>
			    </tr>
			    <tr>
			    	<th scope="row"><label for="akatwitter_oauth_token">Access token</label></th>
			    	<td><input type="text" id="akatwitter_oauth_token" name="akatwitter_oauth_token" value="<?php echo $akatwitter_oauth_token; ?>"></td>
			    </tr>
			    <tr>
			    	<th scope="row"><label for="akatwitter_oauth_token_secret">Access token secret</label></th>
			    	<td><input type="text" id="akatwitter_oauth_token_secret" name="akatwitter_oauth_token_secret" value="<?php echo $akatwitter_oauth_token_secret; ?>"></td>
				</tr>
			</table>
			<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
		</form>
	</div>
	<?php
}


?>
