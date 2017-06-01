<?php
namespace ContentHub;
use MetzWeb\Instagram\Instagram;
use Facebook\Facebook;
/*
Admin class which creates the admin page and the settings page
*/

class AdminSettings {

	public function __construct()
    {
        add_action( 'admin_menu', array($this, 'add_menu_page') );
    }

    // Add admin menu item
    public function add_menu_page()
    {
        add_submenu_page('content_hub', 'Content Hub > Settings', 'Settings', 'edit_theme_options', 'content_hub_settings', array($this, 'content_hub_settings_page'));
        session_start();
    }

    public function content_hub_settings_page()
    {

        if(isset($_POST['submit'])){

            $sanitised = filter_input_array(INPUT_POST,
                array(
                    'facebook_app_id' => FILTER_SANITIZE_STRING,
                    'facebook_app_secret' => FILTER_SANITIZE_STRING,
                    'facebook_access_token' => FILTER_SANITIZE_STRING,
                    'twitter_access_token' => FILTER_SANITIZE_STRING,
                    'twitter_access_token_secret' => FILTER_SANITIZE_STRING,
                    'twitter_consumer_key' => FILTER_SANITIZE_STRING,
                    'twitter_consumer_secret' => FILTER_SANITIZE_STRING,
                    'instagram_api_key' => FILTER_SANITIZE_STRING,
                    'instagram_api_secret' => FILTER_SANITIZE_STRING,
                    'instagram_access_token' => FILTER_SANITIZE_STRING,
                    'youtube_api_key' => FILTER_SANITIZE_STRING
                )
            );
            $additional_types = array();
            foreach($_POST['additional_types'] as $additional_type) {
                $additional_type = Trim($additional_type);
                if(!empty($additional_type)) {
                    $additional_types[] = $additional_type;
                }
            }
            update_option('content_hub_settings', $sanitised);
            update_option('content_hub_additional_types', $additional_types);

        }
        $content_hub_settings = get_option('content_hub_settings');
        $content_hub_additional_types = get_option('content_hub_additional_types');


        if(isset($_GET['code']) && isset($_GET['code_type']) && $_GET['code_type'] == 'instagram') {
            $code = $_GET['code'];
            $instagram = new Instagram(array(
                'apiKey'      => $content_hub_settings['instagram_api_key'],
                'apiSecret'   => $content_hub_settings['instagram_api_secret'],
                'apiCallback' => admin_url( 'admin.php?page=content_hub_settings&code_type=instagram')
            ));
            $data = $instagram->getOAuthToken($code);
            $content_hub_settings['instagram_access_token'] = $data->access_token;
            update_option('content_hub_settings', $content_hub_settings);
        }

        if(isset($_GET['code']) && isset($_GET['code_type']) && $_GET['code_type'] == 'facebook') {
            $facebook  = new Facebook([
                'app_id' => $content_hub_settings['facebook_app_id'],
                'app_secret' => $content_hub_settings['facebook_app_secret'],
                'default_graph_version' => 'v2.5',
            ]);

            $helper = $facebook->getRedirectLoginHelper();
            try {
              $accessToken = $helper->getAccessToken();
            } catch(Facebook\Exceptions\FacebookResponseException $e) {
              // When Graph returns an error
              echo 'Graph returned an error: ' . $e->getMessage();
              exit;
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
              // When validation fails or other local issues
              echo 'Facebook SDK returned an error: ' . $e->getMessage();
              exit;
            }

            //Get a Long-Lived Access Token useing the Short-Lived Access Token
            $cilent = $facebook->getOAuth2Client();
            try {
              // Returns a long-lived access token
              $accessToken = $cilent->getLongLivedAccessToken($accessToken);
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
              // There was an error communicating with Graph
              echo $e->getMessage();
              exit;
            }

            $content_hub_settings['facebook_access_token'] = (string) $accessToken;
            update_option('content_hub_settings', $content_hub_settings);

        }

        if(isset($_GET['logout_facebook']) && $_GET['logout_facebook'] == 1) {
            $content_hub_settings['facebook_access_token'] = '';
            update_option('content_hub_settings', $content_hub_settings);
        }

        if(isset($_GET['logout_instagram']) && $_GET['logout_instagram'] == 1) {
            $content_hub_settings['instagram_access_token'] = '';
            update_option('content_hub_settings', $content_hub_settings);
        }

        ?>
        <div class="wrap content-hub-admin-page">
            <h2>Content Hub > Settings</h2>
            <hr>

            <form method="post" action="admin.php?page=content_hub_settings">

                <!--- FACEBOOK SETTINGS -->
                <h3>Facebook</h3>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row"><label for="facebook_app_id">App ID</label></th>
                            <td><input name="facebook_app_id" id="facebook_app_id" type="text" value="<?php echo $content_hub_settings['facebook_app_id']; ?>"></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label for="facebook_app_secret">App Secret</label></th>
                            <td><input name="facebook_app_secret" id="facebook_app_secret" type="text" value="<?php echo $content_hub_settings['facebook_app_secret']; ?>"></td>
                        </tr>
                        <?php
                        $facebook_access_token = $content_hub_settings['facebook_access_token'];
                        if( $content_hub_settings['facebook_app_id'] &&  $content_hub_settings['facebook_app_secret'] ) {

							try {
	                            $facebook  = new Facebook([
	                              'app_id' => $content_hub_settings['facebook_app_id'],
	                              'app_secret' => $content_hub_settings['facebook_app_secret'],
	                              'default_graph_version' => 'v2.5',
	                            ]);

	                            if(!$facebook_access_token) {

	                                $helper = $facebook->getRedirectLoginHelper();
	                                $permissions = ['user_posts', 'user_photos'];
	                                $loginUrl = $helper->getLoginUrl( admin_url('admin.php?page=content_hub_settings&code_type=facebook') , $permissions );
	                               ?>

	                                <tr valign="top">
	                                    <th scope="row"><label>Access Token</label></th>
	                                    <td><a href="<?php echo $loginUrl ?>">Log in with Facebook</a></td>
	                                </tr>

	                            <?php

	                            } else if($facebook_access_token) {

	                                        try {
	                                            $response =  $facebook->get('/me?fields=id,name', $facebook_access_token);
	                                        } catch(Facebook\Exceptions\FacebookResponseException $e) {
	                                          // When Graph returns an error
	                                          echo 'Graph returned an error: ' . $e->getMessage();

	                                        } catch(Facebook\Exceptions\FacebookSDKException $e) {
	                                          // When validation fails or other local issues
	                                          echo 'Facebook SDK returned an error: ' . $e->getMessage();

	                                        }
	                                        $user = $response->getGraphUser();

	                            ?>
	                                <tr valign="top">
	                                    <th scope="row"><label>Access Token</label></th>
	                                    <td>
	                                        <?php

	                                            $logout_url = admin_url( 'admin.php?page=content_hub_settings&logout_facebook=1');
	                                            echo "Hello! ". $user['name']."! <a href='".$logout_url."'>Log out</a>";
	                                        ?>
	                                    </td>
	                                </tr>

	                            <?php

	                            }
							} catch (\Exception $e) {
								echo $e->getMessage();
							}


                        }
                        ?>
                        <input name="facebook_access_token" id="facebook_access_token" type="hidden" value="<?php echo $facebook_access_token; ?>">
                    </tbody>
                </table>

                <!--- TWITTER SETTINGS -->
                <hr>
                <h3>Twitter</h3>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row"><label for="twitter_access_token">Access Token</label></th>
                            <td><input name="twitter_access_token" id="twitter_access_token" type="text" value="<?php echo $content_hub_settings['twitter_access_token']; ?>"></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label for="twitter_access_token_secret">Access Token Secret</label></th>
                            <td><input name="twitter_access_token_secret" id="twitter_access_token_secret" type="text" value="<?php echo $content_hub_settings['twitter_access_token_secret']; ?>"></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label for="twitter_consumer_key">Consumer Key</label></th>
                            <td><input name="twitter_consumer_key" id="twitter_consumer_key" type="text" value="<?php echo $content_hub_settings['twitter_consumer_key']; ?>"></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label for="twitter_consumer_secret">Consumer Secret</label></th>
                            <td><input name="twitter_consumer_secret" id="twitter_consumer_secret" type="text" value="<?php echo $content_hub_settings['twitter_consumer_secret']; ?>"></td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <h3>Instagram</h3>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row"><label for="instagram_api_key">Client Key</label></th>
                            <td><input name="instagram_api_key" id="instagram_api_key" type="text" value="<?php echo $content_hub_settings['instagram_api_key']; ?>"></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label for="instagram_api_secret">Client Secret</label></th>
                            <td><input name="instagram_api_secret" id="instagram_api_secret" type="text" value="<?php echo $content_hub_settings['instagram_api_secret']; ?>"></td>
                        </tr>
                        <?php
                        $instagram_access_token = $content_hub_settings['instagram_access_token'];
                        if( $content_hub_settings['instagram_api_key'] &&  $content_hub_settings['instagram_api_secret'] ) {
                            $instagram = new Instagram(array(
                                'apiKey'      => $content_hub_settings['instagram_api_key'],
                                'apiSecret'   => $content_hub_settings['instagram_api_secret'],
                                'apiCallback' => admin_url( 'admin.php?page=content_hub_settings&code_type=instagram')
                            ));
                            ?>
                            <tr valign="top">
                                <th scope="row">Access Token</th>
                                <td>
                                    <?php
                                    if(!$instagram_access_token ) {
                                        echo "<a href='".$instagram->getLoginUrl()."'>Login with Instagram</a>";
                                    } else {
                                        $instagram->setAccessToken($instagram_access_token);
                                        $user_instagram = $instagram->getUser();
                                        $logout_url = admin_url( 'admin.php?page=content_hub_settings&logout_instagram=1');
                                        echo "Hello ".$user_instagram->data->full_name."! <a href='".$logout_url."'>Log out</a>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        <input name="instagram_access_token" id="instagram_access_token" type="hidden" value="<?php echo $instagram_access_token; ?>">
                    </tbody>
                </table>
                <hr>
                <h3>Youtube</h3>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row"><label for="youtube_api_key">API Key</label></th>
                            <td><input name="youtube_api_key" id="youtube_api_key" type="text" value="<?php echo $content_hub_settings['youtube_api_key']; ?>"></td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <h3>Additional Types</h3>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row"><label for="additional_types">Add Types</label></th>
                            <td id="additional_types">
                                <?php
                                $counter = 0;
                                foreach($content_hub_additional_types as $additional_type) {
                                    $counter++;
                                    ?>
                                    <span>
                                        <input name="additional_types[]" type="text" value="<?php echo $additional_type; ?>">
                                        <?php
                                        if($counter == sizeof($content_hub_additional_types)) {
                                            ?> <button class="button button-secondary">+</button><?php
                                        }
                                        ?>
                                    </span>
                                    <?php
                                }
                                if(!$content_hub_additional_types || sizeof($content_hub_additional_types) == 0) {
                                    ?>
                                    <span><input name="additional_types[]" type="text" value=""> <button class="button button-secondary">+</button></span>
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

        </div>
        <?php
    }

}
