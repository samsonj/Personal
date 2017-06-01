<?php

namespace ContentHub;

/*
Admin class which creates the admin page and the settings page
*/

class Admin {

	public function __construct()
    {
        add_action( 'admin_enqueue_scripts',  array($this, 'include_js'));
        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'add_menu_page') );
        $AdminSettings = new AdminSettings();
        $AdminAjax = new AdminAjax();
    }

    // Include JS
    public function include_js() {
        wp_enqueue_script('jquery-ui-sortable', CONTENTHUB_URL.'/library/scripts/jquery.sortable.min.js', array('jquery'));
        wp_enqueue_script('contenthub-datetime', CONTENTHUB_URL.'/library/scripts/jquery.datetimepicker.js', array('jquery'));
        wp_enqueue_script('contenthub-admin', CONTENTHUB_URL.'/library/scripts/admin.js', array('jquery'));
        wp_enqueue_media();
    }

    // Include CSS
    public function admin_init() {
        wp_enqueue_style('contenthub-admin', CONTENTHUB_URL.'/library/styles/admin.css');
        wp_enqueue_style('contenthub-datetime', CONTENTHUB_URL.'/library/styles/jquery.datetimepicker.css');
    }

    // Add admin menu item
    public function add_menu_page()
    {
        add_menu_page( 'Content Hub > Feed', 'Content Hub', 'edit_pages', 'content_hub', array( $this, 'content_hub_page' ), 'dashicons-schedule' );
        add_submenu_page('content_hub', 'Content Hub > Feed', 'Feed', 'edit_pages', 'content_hub', array($this, 'content_hub_page'));
        add_submenu_page('content_hub', 'Content Hub > Help', 'Help', 'edit_theme_options', 'content_hub_help', array($this, 'content_hub_help_page'));
    }

    // Add admin page content
    public function content_hub_page()
    {
    	?>
        <div class="wrap content-hub-admin-page">

            <h2>Content Hub</h2>
            <hr>
            <h3>Add an item</h3>
            <p class="content-hub-choose-method"><label><input checked type="radio" name="content-hub-add-method" value="link">By social link</label><label><input type="radio" name="content-hub-add-method" value="manual">Manually</label></p>
            <div class="content-hub-form-link-container">
                <p>Paste a link in the box below. Supported: Facebook status, Tweet, Instagram item, Youtube video.</p>
                <form class="content-hub-form-link" method="POST">
                    <input type="text" placeholder="Ex: https://twitter.com/TestOfTweet/status/123456789123456789">
                    <div class="message"></div>
                </form>
            </div>
            <div class="content-hub-form-manual-container">
                <p>Fill up the information below</p>
                <form class="content-hub-form-manual" method="POST">
                    <table>
                        <tr>
                            <th>Type</th>
                            <td>
                            <select name="type">
                                <option value="facebook">Facebook</option>
                                <option value="twitter">Twitter</option>
                                <option value="instagram">Instagram</option>
                                <option value="googleplus">Google+</option>
                                <option value="youtube">Youtube</option>
                                <option value="content">Content</option>
                                <?php
                                $additional_types = get_option('content_hub_additional_types');
                                foreach($additional_types as $additional_type) {
                                    ?>
                                    <option value="<?php echo $additional_type; ?>"><?php echo ucfirst($additional_type); ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            </td>
                        </tr>
                        <tr>
                            <th valign="top">Description</th>
                            <td>
                                <?php wp_editor('', 'description', array('quicktags' => true, 'media_buttons' => false)); ?>
                            </td>
                        </tr>
                        <tr>
                            <th valign="top">Username</th>
                            <td><input type="text" name="username"></td>
                        </tr>
                        <tr>
                            <th valign="top">Date</th>
                            <td><input type="text" name="date" placeholder="0000-00-00 00:00:00"></td>
                        </tr>
                        <tr>
                            <th valign="top">Image</th>
                            <td>
                                <div class="content-hub-image-container">
                                    <a href="#" class="content-hub-image-remove hide">X</a>
                                    <input type="hidden" name="image" class="tocontent-hub-image" />
                                    <input type="button" class="button content-hub-image-button" value="Select image" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th valign="top">Link</th>
                            <td><input type="text" name="link"></td>
                        </tr>
                        <tr>
                            <th valign="top">CSS Classes</th>
                            <td><input type="text" name="css_classes"></td>
                        </tr>
                    </table>
                    <br>
                    <input type="submit" class="button button-primary" value="Add this item"><div class="message"></div>

                </form>
            </div>
            <hr>
            <h3>Feed</h3>
            <p>Click on an item to view/edit/delete it. You can also drag and drop the items to re-order them.</p>

            <div class="content-hub-feed"></div>

        </div>

    	<?php

    }

    // Add Help Page
    public function content_hub_help_page()
    {
        ?>
        <div class="wrap content-hub-admin-page">
            <h2>Content Hub > Help</h2>
            <hr>
            <h3>Sample Content</h3>
            <p>You can generate some sample content by clicking the button below. An item of each basic type will be added to the feed.</p>
            <div id="sample-content-container"><button class="button button-primary" id="generate-sample-content">Generate sample content</button></div>
            <hr>
            <h3>Use in the theme</h3>
            <p>You can copy and paste the below php snippet in your theme templates. It should cover everything, if you need more help, ask a back-end developer</p>
            <pre>
                <?php
                echo htmlentities('
<?php
if(function_exists("get_contenthub_feed")) {
    // The function below can take two parameters: $limit and $offset, for pagination
    // Ex: $feed = get_contenthub_feed(5, 10); ==> Get the second page of results with 5 items per page
    $feed = get_contenthub_feed();
    if(!empty($feed)) {
        ?>
        <div id="content-hub-feed">
            <?php
            foreach($feed as $item) {

                // Below an example on how to display each variable
                ?>
                <div class="feed-item <?php echo $item->type; ?>">
                    <a href="<?php echo $item->link; ?>" target="_blank">
                        <div class="image">
                            <?php
                            $image = wp_get_attachment_image_src($item->image, "thumbnail");
                            $image_url = $image[0];
                            ?>
                            <img src="<?php echo $image_url; ?>">
                        </div>
                        <div class="description"><?php echo $item->description; ?></div>
                        <div class="username"><?php echo $item->username; ?></div>
                        <div class="date"><?php echo $item->date; ?></div>
                    </a>
                </div>
                <?php

                // Here is how to test if a variable exists
                if($item->link) {
                    // Do something is link exists
                    ?><p>Some markup</p><?php
                } else {
                    // Do something else
                    ?><p>Some markup</p><?php
                }

                // Here is how to do different things depending on the type
                switch($item->type) {
                    case "facebook":
                        // Do the facebook stuff
                        ?><p>Some markup</p><?php
                        break;
                    case "twitter":
                        // Do the twitter stuff
                        ?><p>Some markup</p><?php
                        break;
                    case "content":
                        // Do the content stuff
                        ?><p>Some markup</p><?php
                        break;
                }

            }
            ?>
        </div>
        <?php
    }
}
?>
                ');
                ?>
            </pre>
        </div>
        <?php
    }

}
