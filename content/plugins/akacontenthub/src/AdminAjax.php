<?php

namespace ContentHub;

/*
AdminAjax class which adds all the ajax actions needed
*/

class AdminAjax {

    public $settings;

	public function __construct() {
        add_action( 'wp_ajax_contenthub_check_url', array($this, 'check_url') );
        add_action( 'wp_ajax_contenthub_add_item', array($this, 'add_item') );
        add_action( 'wp_ajax_contenthub_get_item', array($this, 'get_item') );
        add_action( 'wp_ajax_contenthub_edit_item_form', array($this, 'edit_item_form') );
        add_action( 'wp_ajax_contenthub_edit_item', array($this, 'edit_item') );
        add_action( 'wp_ajax_contenthub_remove_item', array($this, 'remove_item') );
        add_action( 'wp_ajax_contenthub_order_items', array($this, 'order_items') );
        add_action( 'wp_ajax_contenthub_list_item', array($this, 'list_items') );
        add_action( 'wp_ajax_contenthub_generate_sample_content', array($this, 'generate_sample_content') );
        $this->settings = get_option('content_hub_settings');
    }

    public function check_url() {

        global $post;

        $url = $_POST['url'];
        $return = array();

        if (!filter_var($url, FILTER_VALIDATE_URL)) {

            // Not a valid URL
            $return['error_message'] = "The URL is not in a valid format";

        } else {

            if(preg_match("|^https?://(www\.)?twitter\.com/(.+)/status/([0-9]+)|", $url, $matches)) {
                $return['type'] = 'twitter';
                $return['id'] = $matches[3];
            }
            if(preg_match("|^https?://(www\.)?facebook\.com/(.+)/posts/([0-9]+)|", $url, $matches)) {
                $return['type'] = 'facebook';
                $return['id'] = $matches[3];
                $return['feedtype'] = "posts";

            }
            if(preg_match("|^https?://(www\.)?facebook\.com/(.+)/photos/(.+)\.(.+)\.(.+)\.(.+)/(.+)/|", $url, $matches)) {
                $return['type'] = 'facebook';
                $return['id'] = $matches[7];
                $return['feedtype'] = "photos";
            }
            if(preg_match("|^https?://(www\.)?instagram\.com/p/(.+)/|", $url, $matches)) {
                $return['type'] = 'instagram';
                $return['id'] = $matches[2];
            }
            if(preg_match("|^https?://(www\.)?youtube\.com/watch\?v=(.+)|", $url, $matches)) {
                $return['type'] = 'youtube';
                $return['id'] = $matches[2];
            }
            if(preg_match("|^https?://(www\.)?youtu\.be/(.+)|", $url, $matches)) {
                $return['type'] = 'youtube';
                $return['id'] = $matches[2];
            }
            if(empty($return)) {
                $return['error_message'] = "URL type not recognised, please paste only facebook, twitter, instagram or youtube single URL";
            }

        }

        echo json_encode($return);
        die();

    }

    public function add_item() {
        global $post;

        $type = $_POST['type'];
        $objectItem = $_POST['object'];
        $feedtype = $_POST['feedtype'];
        $manual = $_POST['manual'];
        $return = array();

        if($manual == '1') {
             // JSON object provided, so manual
            $feedItem = new FeedItem\FeedItem();
            $feedItem->type = $objectItem['type'];
            $feedItem->description = $objectItem['description'];
            $feedItem->username = $objectItem['username'];
            $feedItem->date = $objectItem['date'];
            $feedItem->image = $objectItem['image'];
            $feedItem->link = $objectItem['link'];
            $feedItem->css_classes = $objectItem['css_classes'];
        } else {
            // Timestamp offset calculation
            $gmt_offset = get_option('gmt_offset');
            $time_offset = $gmt_offset * 3600;
            // Regex to replace text links to anchor tags in the description
            $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
            $reg_exUrl_last = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?$/";

            // ID provided, so social link
            switch($type) {
                case 'facebook':

                    $feedItem = new FeedItem\FeedItemFacebook($this->settings['facebook_app_id'], $this->settings['facebook_app_secret'], $this->settings['facebook_access_token'] );
                    $status = $feedItem->getStatus($objectItem, $feedtype );
                    // echo "<pre>";var_dump($status);echo "</pre>";
                    if($status === false) {
                        $return['error_message'] = 'There are some problems with the Facebook authentication, please check the <a href="admin.php?page=content_hub_settings">settings</a>';
                        $feedItem = null;
                    } else {
                        // echo "<pre>";var_dump($status['source']);echo "</pre>";exit;
                        $description = '';
                        if(isset($status['message'])) {
                            $description = $status['message'];
                        }
                        if(!$description && isset($status['name'])) {
                            $description = $status['name'];
                        }
                        if(isset($status['updated_time'])) {
                            $timestamp  =  $status['updated_time'];
                        }elseif (isset($status['created_time'])) {
                            $timestamp  =  $status['created_time'];
                        }
                        if(isset($status['source'])) {
                            $image_url = $status['source'];
                            //check if source is an image
                            $url_headers = get_headers($image_url, 1);

                            if(isset($url_headers['Content-Type'])){

                                $type=strtolower($url_headers['Content-Type']);
                                $valid_image_type=array();
                                $valid_image_type['image/png']='';
                                $valid_image_type['image/jpg']='';
                                $valid_image_type['image/jpeg']='';
                                $valid_image_type['image/jpe']='';
                                $valid_image_type['image/gif']='';
                                $valid_image_type['image/tif']='';
                                $valid_image_type['image/tiff']='';
                                $valid_image_type['image/svg']='';
                                $valid_image_type['image/ico']='';
                                $valid_image_type['image/icon']='';
                                $valid_image_type['image/x-icon']='';

                                if(isset($valid_image_type[$type])){
                                    $image = $this->add_image($image_url, $objectItem.'.jpg');
                                    $feedItem->image = $image;
                                }
                            }
                        }

                        $timestamp = strtotime($timestamp->format('Y-m-d H:i:s'));
                        $timestamp = $timestamp + $time_offset;
                        $date = date('Y-m-d H:i:s', $timestamp);

                        $feedItem->type = 'facebook';
                        $feedItem->description = $description;
                        $feedItem->username = $status['from']['name'];
                        $feedItem->date = $date;
                        $feedItem->link = 'https://www.facebook.com/'.$objectItem;

                    }
                    break;
                case 'twitter':
                    $feedItem = new FeedItem\FeedItemTwitter(
                        $this->settings['twitter_access_token'],
                        $this->settings['twitter_access_token_secret'],
                        $this->settings['twitter_consumer_key'],
                        $this->settings['twitter_consumer_secret']
                    );
                    $tweet = $feedItem->getTweet($objectItem);
                    if(isset($tweet->errors)) {
                        $return['error_message'] = 'There are some problems with Twitter, please check the <a href="admin.php?page=content_hub_settings">settings</a> and paste a twitter status link';
                        $feedItem = null;
                    } else {
                        $description = '';
                        if(isset($tweet->text)) {
                            $description = $tweet->text;
                        }
                        // Fix for additional tweet URL coming from the API
                        $description = preg_replace($reg_exUrl_last, '', $description);
                        if(preg_match($reg_exUrl, $description, $url)) {
                            $description  = preg_replace($reg_exUrl, '<a href="'.$url[0].'" rel="nofollow" target="_blank">'.$url[0].'</a>', $description);
                        }
                        $timestamp = strtotime($tweet->created_at);
                        $timestamp = $timestamp + $time_offset;
                        $date = date('Y-m-d H:i:s', $timestamp);
                        $image = '';
                        if(isset($tweet->extended_entities->media[0]) && $tweet->extended_entities->media[0]->type = 'photo') {
                            $image_url = $tweet->extended_entities->media[0]->media_url;
                            $image = $this->add_image($image_url);
                        }
                        $feedItem->type = 'twitter';
                        $feedItem->description = $description;
                        $feedItem->username = $tweet->user->screen_name;
                        $feedItem->date = $date;
                        $feedItem->image = $image;
                        $feedItem->link = 'https://twitter.com/'.$tweet->user->screen_name.'/status/'.$objectItem;
                    }
                    break;
                case 'instagram':
                    $feedItem = new FeedItem\FeedItemInstagram(
                        $this->settings['instagram_api_key'],
                        $this->settings['instagram_api_secret'],
                        $this->settings['instagram_access_token']
                    );
                    $instagramObj = $feedItem->getMedia($objectItem);
                    $timestamp = $instagramObj->data->created_time;
                    $timestamp = $timestamp + $time_offset;
                    $image = $this->add_image($instagramObj->data->images->standard_resolution->url);
                    $date = date('Y-m-d H:i:s', $timestamp);
                    $feedItem->type = 'instagram';
                    $feedItem->description = $instagramObj->data->caption->text;
                    $feedItem->username = $instagramObj->data->user->username;
                    $feedItem->date = $date;
                    $feedItem->image = $image;
                    $feedItem->link = $instagramObj->data->link;
                    break;
                case 'youtube':
                    $feedItem = new FeedItem\FeedItemYoutube(
                        $this->settings['youtube_api_key']
                    );
                    $youtubeObj = $feedItem->getVideo($objectItem);
                    $timestamp = strtotime($youtubeObj['date']);
                    $timestamp = $timestamp + $time_offset;
                    $date = date('Y-m-d H:i:s', $timestamp);
                    $image = $this->add_image($youtubeObj['image'], $youtubeObj['id'].'.jpg');
                    $feedItem->type = 'youtube';
                    $feedItem->description = $youtubeObj['title'];
                    $feedItem->username = $youtubeObj['channel'];
                    $feedItem->date = $date;
                    $feedItem->image = $image;
                    $feedItem->link = 'https://www.youtube.com/watch?v='.$youtubeObj['id'];
                    break;
            }
        }

        if(is_object($feedItem)) {
            $dbObject = new Database();
            if($dbObject->insertFeedItem($feedItem)) {
                $return['success'] = true;
            }
        }

        echo json_encode($return);
        die();

    }


    public function generate_sample_content() {

        $dbObject = new Database();

        // Facebook
        $feedItem = new FeedItem\FeedItem();
        $feedItem->type = 'facebook';
        $feedItem->description = 'This is a facebook status, consectetur adipiscing elit. Cras eget ex vitae leo consequat pellentesque eget a mauris. Aliquam eu auctor lectus.';
        $feedItem->username = 'John Doe';
        $feedItem->date = '1986-11-01 13:30:00';
        $image = $this->add_image(CONTENTHUB_URL.'library/images/facebook.jpg', 'facebook.jpg');
        $feedItem->image = $image;
        $feedItem->link = 'http://www.facebook.com/';
        $dbObject->insertFeedItem($feedItem);

        // Twitter
        $feedItem = new FeedItem\FeedItem();
        $feedItem->type = 'twitter';
        $feedItem->description = 'This is a tweet! Aliquam eu auctor lectus.';
        $feedItem->username = 'Janedoe';
        $feedItem->date = '1990-09-10 12:05:00';
        $image = $this->add_image(CONTENTHUB_URL.'library/images/twitter.jpg', 'twitter.jpg');
        $feedItem->image = $image;
        $feedItem->link = 'http://twitter.com/';
        $dbObject->insertFeedItem($feedItem);

        // Instagram
        $feedItem = new FeedItem\FeedItem();
        $feedItem->type = 'instagram';
        $feedItem->description = '#instagramhastag';
        $feedItem->username = 'Instardoe';
        $feedItem->date = '2015-06-25 16:50:55';
        $image = $this->add_image(CONTENTHUB_URL.'library/images/instagram.jpg', 'instagram.jpg');
        $feedItem->image = $image;
        $feedItem->link = 'http://instagram.com/';
        $dbObject->insertFeedItem($feedItem);

        // Youtube
        $feedItem = new FeedItem\FeedItem();
        $feedItem->type = 'youtube';
        $feedItem->description = '';
        $feedItem->username = 'AKAWebteam';
        $feedItem->date = '2015-07-25 12:30:10';
        $image = $this->add_image(CONTENTHUB_URL.'library/images/youtube.jpg', 'youtube.jpg');
        $feedItem->image = $image;
        $feedItem->link = 'http://www.youtube.com/';
        $dbObject->insertFeedItem($feedItem);

        // Google +
        $feedItem = new FeedItem\FeedItem();
        $feedItem->type = 'googleplus';
        $feedItem->description = 'This is a tweet! Aliquam eu auctor lectus. This is a tweet! Aliquam eu auctor lectus. This is a tweet! Aliquam eu auctor lectus.';
        $feedItem->username = 'JohnDoe';
        $feedItem->date = '2014-08-25 10:30:00';
        $image = $this->add_image(CONTENTHUB_URL.'library/images/googleplus.jpg', 'googleplus.jpg');
        $feedItem->image = $image;
        $feedItem->link = 'http://www.youtube.com/';
        $dbObject->insertFeedItem($feedItem);

        // News
        $feedItem = new FeedItem\FeedItem();
        $feedItem->type = 'content';
        $feedItem->description = '';
        $feedItem->username = 'This is a tweet! Aliquam eu auctor lectus. This is a tweet! Aliquam eu auctor lectus. This is a tweet! Aliquam eu auctor lectus. This is a tweet! Aliquam eu auctor lectus.';
        $feedItem->date = '2014-08-25 10:30:00';
        $image = $this->add_image(CONTENTHUB_URL.'library/images/news.jpg', 'news.jpg');
        $feedItem->image = $image;
        $feedItem->link = get_bloginfo('siteurl');
        $dbObject->insertFeedItem($feedItem);

        die();

    }

    public function add_image($image_url, $filename = '') {
        global $post;

        $upload_dir = wp_upload_dir();
        $image_url = explode('?', $image_url);
        $image_url = $image_url[0];
        $contenthub_upload_dir = $upload_dir['basedir'].'/contenthub/';
        if(!is_dir($contenthub_upload_dir)) {
            mkdir($contenthub_upload_dir);
        }
        if(!$filename)
            $filename = basename( $image_url );
        file_put_contents($contenthub_upload_dir.$filename, file_get_contents($image_url));
        $filetype = wp_check_filetype( $filename, null );
        if(!$filetype['type']) {
            $filetype['ext'] = 'jpg';
            $filetype['type'] = 'image/jpeg';
        }
        $attachment = array(
            'guid'           => $contenthub_upload_dir.$filename,
            'post_mime_type' => $filetype['type'],
            'post_title'     => $filename,
            'post_content'   => '',
            'post_status'    => 'inherit'
        );
        $attach_id = wp_insert_attachment($attachment, $contenthub_upload_dir.$filename);
        $attach_data = wp_generate_attachment_metadata( $attach_id, $contenthub_upload_dir.$filename );
        wp_update_attachment_metadata( $attach_id, $attach_data );
        return $attach_id;

    }

    public function remove_item() {
        global $post;

        $idFeedItem = $_POST['id'];

        if(isset($idFeedItem) && is_numeric($idFeedItem)) {
            $dbObject = new Database();
            $dbObject->removeFeedItem($idFeedItem);
        }

        die();
    }

    public function edit_item_form() {
        global $post;

        $idFeedItem = $_POST['id'];

        if(isset($idFeedItem) && is_numeric($idFeedItem)) {
            $dbObject = new Database();
            $feedItem = $dbObject->getFeedItem($idFeedItem);
            if($feedItem->image > 0) {
                $image_title = get_the_title($feedItem->image);
            }
            if(!empty($feedItem)) {
                ?>
                <div class="edit-feed-item" data-id="<?php echo $idFeedItem; ?>">
                    <h3>Editing this item</h3>
                    <p>
                        <strong>Type: </strong>
                        <select class="feed-item-type">
                            <option <?php if($feedItem->type == 'facebook') echo "selected"; ?> value="facebook">Facebook</option>
                            <option <?php if($feedItem->type == 'twitter') echo "selected"; ?> value="twitter">Twitter</option>
                            <option <?php if($feedItem->type == 'instagram') echo "selected"; ?> value="instagram">Instagram</option>
                            <option <?php if($feedItem->type == 'googleplus') echo "selected"; ?> value="googleplus">Google+</option>
                            <option <?php if($feedItem->type == 'youtube') echo "selected"; ?> value="youtube">Youtube</option>
                            <option <?php if($feedItem->type == 'content') echo "selected"; ?> value="content">Content</option>
                            <?php
                                $additional_types = get_option('content_hub_additional_types');
                                foreach($additional_types as $additional_type) {
                                    ?>
                                    <option <?php if($feedItem->type == $additional_type) echo "selected"; ?> value="<?php echo $additional_type; ?>"><?php echo ucfirst($additional_type); ?></option>
                                    <?php
                                }
                                ?>
                        </select>
                    </p>
                    <p>
                        <strong>Description: </strong><br />
                        <textarea class="feed-item-description" cols="60" rows="5"><?php echo $feedItem->description; ?></textarea>
                    </p>
                    <p>
                        <strong>Username: </strong>
                        <input type="text" class="feed-item-username" value="<?php echo $feedItem->username; ?>">
                    </p>
                    <p>
                        <strong>Date: </strong>
                        <input type="text" class="feed-item-date" value="<?php echo $feedItem->date; ?>">
                    </p>
                    <p>
                        <strong>Link: </strong>
                        <input type="text" class="feed-item-link" value="<?php echo $feedItem->link; ?>">
                    </p>
                    <p>
                        <div class="content-hub-image-container">
                            <strong>Image: </strong>
                            <a href="#" class="content-hub-image-remove <?php if($feedItem->image == 0 || empty($feedItem->image)) { echo 'hide'; } ?>">X</a>
                            <?php if($image_title) { ?><span class="content-hub-image-name"><?php echo $image_title; ?></span><?php } ?>
                            <input type="hidden" name="image" class="tocontent-hub-image" value="<?php echo $feedItem->image; ?>" />
                            <input type="button" class="button content-hub-image-button" value="Select image" />
                        </div>
                    </p>
                    <p>
                        <strong>CSS Classes: </strong>
                        <input type="text" class="feed-item-css-classes" value="<?php echo $feedItem->css_classes; ?>"  >
                    </p>
                    <p>
                        <a href="#" class="feed-item-edit-cancel button button-secondary">Cancel</a>
                        <a href="#" class="feed-item-edit-update button button-primary">Update</a>
                    </p>
                </div>
                <?php
            }
        }

        die();
    }

    public function get_item() {
        global $post;

        $idFeedItem = $_POST['id'];

        if(isset($idFeedItem) && is_numeric($idFeedItem)) {
            $dbObject = new Database();
            $feedItem = $dbObject->getFeedItem($idFeedItem);
            if(!empty($feedItem)) {
                $this->display_item_admin($feedItem);
            }
        }

        die();
    }

    public function edit_item() {
        global $post;

        $idFeedItem = $_POST['id'];

        if(isset($idFeedItem) && is_numeric($idFeedItem)) {
            $dbObject = new Database();
            $feedItem = $dbObject->getFeedItem($idFeedItem);
            if(!empty($feedItem)) {
                $feedItem->type = $_POST['type'];
                $feedItem->description = stripslashes($_POST['description']);
                $feedItem->username = $_POST['username'];
                $feedItem->date = $_POST['date'];
                $feedItem->link = $_POST['link'];
                $feedItem->image = $_POST['image'];
                $feedItem->css_classes = $_POST['css_classes'];
                if($dbObject->updateFeedItem($feedItem)) {
                    $this->display_item_admin($feedItem);
                }
            }
        }

        die();
    }

    public function order_items() {

        global $post;

        $order = $_POST['order'];

        if(is_array($order)) {
            $dbObject = new Database();
            $i = 0;
            foreach($order as $item) {
                $dbObject->updateFeedItemOrder($item, $i);
                $i++;
            }
        }

        die();

    }

    public function list_items() {

        global $post;

        $dbObj = new Database();
        $feed = $dbObj->get_contenthub_feed();
        if(!empty($feed)) {
            ?>
            <div class="feed-items">
                <?php
                foreach($feed as $item) {
                    ?>
                    <div class="feed-item" id="feed-item-<?php echo $item->id; ?>">
                        <?php
                        $this->display_item_admin($item);
                        ?>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
        }

        die();

    }

    public function display_item_admin($item) {
        $image_url = wp_get_attachment_url($item->image);
        $image = wp_get_attachment_image($item->image, 'thumbnail');
        $image = '<a href="'.$image_url.'" target="_blank">'.$image.'</a>';
        ?>
            <div class="feed-item-left">
                 <?php if($item->image) { echo $image; } else { echo 'None'; } ?>
            </div>
            <div class="feed-item-right">
                <p><strong>Type:</strong> <?php echo $item->type; ?></p>
                <p><strong>Description:</strong> <?php echo $item->description; ?></p>
                <p><strong>Username:</strong> <?php echo $item->username; ?></p>
                <p><strong>Date:</strong> <?php echo $item->date; ?></p>
                <p><strong>Link:</strong> <?php if($item->link) { echo '<a href="'.$item->link.'" target="_blank">'.$item->link.'</a>'; } ?></p>
                <p><strong>CSS Classes:</strong> <?php echo $item->css_classes; ?></p>
            </div>

            <div style="clear:both"></div>

            <a href="#" data-id="<?php echo $item->id; ?>" class="edit"><i class="fa fa-edit"></i></a>
            <a href="#" data-id="<?php echo $item->id; ?>" class="remove"><i class="fa fa-trash"></i></a>
        <?php
    }

}
