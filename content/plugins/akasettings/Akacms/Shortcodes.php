<?php
/**
 * AKA CMS Shortcodes class
 */
class Akacms_Shortcodes {

    public static
    $instance = null;


    /**
     * Object constructor
     */
    function __construct() {
        self::$instance = $this;

        // Add shortcodes
        add_shortcode('akaBookTickets', array($this, 'display_book_tickets'));
        add_shortcode('akabooktickets', array($this, 'display_book_tickets'));
        add_shortcode('h1_value', array($this, 'display_h1_value'));
        add_shortcode('template', array($this, 'display_template'));
        add_shortcode('blogpath', array($this, 'display_blogpath'));
        add_shortcode('blog', array($this, 'display_blogpath'));
        add_shortcode('akaJsEmail', array($this, 'display_email'));
        add_shortcode('aka_share', array($this, 'display_share_url'));
        add_shortcode('aka_iframe', array($this, 'display_iframe'));

    }

    /**
    * Display Book Tickets
    */
    public function display_book_tickets($atts) {
        extract( shortcode_atts( array(
            'button'    => '1',
            'text'      => 'Book Tickets',
            'class'     => 'btnBookTickets'
        ), $atts ) );

        $book_url = get_option('book_url');

        if(!empty($atts['button'])){
            return '<a href="'.$book_url.'" class="'.$atts['class'].'">'.$atts['text'].'</a>';
        } else {
            return $book_url;
        }
    }

    /**
    * Display H1 Value
    */
    public function display_h1_value($atts) {
        global $post;

        $h1_value = get_option('h1_value');
        $return_value = str_replace('%page_title%', apply_filters('the_title', $post->post_title), $h1_value);

        $url = str_replace(get_bloginfo('url'), '', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"]);

        $h1_exceptions = json_decode(get_option('h1_exceptions'));
        $alt_return = false;

        if(!empty($h1_exceptions)) {
            foreach($h1_exceptions as $h1_exception) {
                if(substr($h1_exception->url, -1) == '*') {
                    if(strpos($url, str_replace('*', '', $h1_exception->url)) === 0) {
                        $alt_return = true;
                        break;
                    }
                } else {
                    if($url == $h1_exception->url) {
                        $alt_return = true;
                        break;
                    }
                }
            }
            if($alt_return) {
                $return_value = str_replace('%page_title%', apply_filters('the_title', $post->post_title), $h1_exception->value);
            }
        }

        return $return_value;
    }

    /**
    * Display Template
    */
    public function display_template() {
        return get_bloginfo('template_directory');
    }

    /**
    * Display Template
    */
    public function display_blogpath() {
        return get_bloginfo('url');
    }

    /**
    * Display Template
    */
    public function display_email($atts) {
        extract( shortcode_atts( array(
            'email'     => '',
            'nolink'    => 0
        ), $atts ) );
        $emaillen = strlen($email);
        $emailnbchar = ceil($emaillen/3);
        $emailstr1 = substr($email, 0, $emailnbchar);
        $emailstr2 = substr($email, $emailnbchar, $emailnbchar);
        $emailstr3 = substr($email, ($emailnbchar*2), $emailnbchar);
        ob_start();
        ?>
        <script type="text/javascript">
            var stre = '<?php echo $emailstr1; ?>' + '<?php echo $emailstr2; ?>' + '<?php echo $emailstr3; ?>';
            <?php
            if($nolink == 1) {
                ?>document.write(stre);<?php
            } else {
                ?>document.write('<a href="mailto:' + stre + '">' + stre + '</a>');<?php
            }
            ?>
        </script>
        <?php
        return ob_get_clean();
    }

    /**
    * Display Share URL
    */
    public function display_share_url($atts, $content = null) {
        extract( shortcode_atts( array(
            'network'     => '',
            'url'     => '',
            'description'    => '',
            'title'    => '',
            'classes'    => ''
        ), $atts ) );
        return '<a class="'.$classes.'" href="'.$this->get_share_url($network, $url, $description, $title).'"" target="_blank">'.$content.'</a>';
    }

    public static function get_share_url($network, $url = '', $description = '', $title = '') {

        if(!$url) {
            $url = get_permalink();
        }
        $url = urlencode($url);
        $body = $description." ".$url;
        $description = urlencode($description);

        switch($network) {
            case 'facebook':
                return "https://www.facebook.com/sharer/sharer.php?u=".$url;
            case 'googleplus':
                return "https://plus.google.com/share?url=".$url;
            case 'twitter':
                return "https://www.twitter.com/share?url=".$url."&text=".$description;
            case 'tumblr':
                return "http://www.tumblr.com/share/link?url=".$url."&description=".$description;
            case 'pinterest':
                return "https://www.pinterest.com/pin/create/button/?url=".$url."&description=".$description;
            case 'email':
                return "mailto:?subject=".$title."&body=".$body;
        }

        return false;

    }

    public function display_iframe($atts, $content = null) {

        extract( shortcode_atts( array(
            'url'     => '',
            'percentage'    => '56.5'
        ), $atts ) );

        $html = '<div style="position:relative;padding-bottom:' . $percentage . '%;height:0;width:100%;display:block;clear:both;margin: 20px auto;" class="cf clearfix">';
        $html .= '<iframe src="' . $url . '" frameborder="0" allowfullscreen style="position:absolute;top:0;left:0;margin:0;padding:0;display:block;width:100%;height:100%;"></iframe>';
        $html .= '</div>';

        return $html;

    }


}
