<?php
/*
Plugin Name: AKA Taxonomy Post Order
Plugin URI: http://www.akauk.com
Description: Add the ability to sort posts within taxonomies
Version: 0.2
Author: Matt Cegielka
Author URI: http://www.akauk.com
*/

define('AKA_POSTORDERINTAXONOMY_VERSION', 0.2);

// Menu items
add_action('admin_menu', 'aka_postorder_taxonomy_custom_order_menu_items');
function aka_postorder_taxonomy_custom_order_menu_items() {
    // $types = get_post_types(array('public' => true));
    $types = get_post_types();
    
    foreach ($types as $type) {
        $categories = get_taxonomies(array('object_type'=> array($type)), 'objects');
        // var_dump($categories);
        foreach ($categories as $category) {
            add_submenu_page('edit.php?post_type='.$type, "{$category->labels->singular_name} reorder", "Reorder by ".$category->labels->singular_name, 'edit_pages', "akataxorder-{$type}|{$category->name}", 'aka_postorder_taxonomy_custom_order_menu_page');
        }
    }

}

// Display the reorder page
function aka_postorder_taxonomy_custom_order_menu_page() {
    global $plugin_page;
    global $wpdb;

    // Find out what taxonomy we're editing
    $posttype = $_GET['post_type'];
    $taxmatch = array();
    preg_match("@akataxorder-$posttype\|([^\&]+)@", $plugin_page, $taxmatch);

    $taxname = count($taxmatch) > 1 ? $taxmatch[1] : false;
    $terms = get_categories(array('taxonomy' => $taxname));
    // var_dump($terms);

    // Check if we're already getting posts for a term
    $termid = $_GET['term'];
    $posts = false;
    if ($termid) {
        $args = array(
            'posts_per_page' => -1,
            'post_type' => $posttype,
            'tax_query' => array(
                array('taxonomy' => $taxname, 'terms' => array($termid))),
            'orderby'   => 'meta_value_num',
            'order'     => 'ASC',
            'meta_key'  => '_tax_order_'.$termid
        );
        $posts = get_posts($args);
        if(empty($posts)) {
            unset($args['orderby'], $args['order'], $args['meta_key']);
            $posts = get_posts($args);
        }
    }

    ?>
    <div class="wrap">
        <div class="icon32" id="icon-edit"><br></div>
        <h2>Reorder</h2>

        <p>Choose a group to re-order posts by:</p>
        <form action="<?php get_admin_url(); ?>" method="GET">
            <input type="hidden" name="post_type" value="<?php echo esc_attr($posttype); ?>">
            <input type="hidden" name="page" value="<?php echo esc_attr($plugin_page); ?>">
            <select name="term">
                <option value="">Choose</option>
                <?php foreach ($terms as $term): ?>
                <option <?php echo ($term->term_id == $termid) ? 'selected="selected"':""; ?> value="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="button-primary" id="postsorder_select_tax">Reorder posts</button>
        </form>
        <hr />
        <?php if ($posts): ?>
        <ul id="postsorder">
            <?php foreach ($posts as $p): ?>
            <li id='order_<?php echo $p->ID; ?>'><span><?php echo $p->post_title; ?></span></li>
            <?php endforeach; ?>
        </ul>
        <div id="ajax-response"></div>
        <input type="hidden" id="save_term_id" value="<?php echo esc_attr($termid); ?>" />
        <button type="button" class="button-primary" id="postsorder_save">Save order</button>
        <?php endif; ?>
    </div>

    <script type="text/javascript">
    (function($) {
        $(function() {
            $('#postsorder').sortable();
            $('#postsorder_save').on('click', function(e) {
                $.ajax({
                    'type': 'POST',
                    url: ajaxurl,
                    data: {action:'aka-postorder-tax-save-order', term: $('#save_term_id').val(), order: $('#postsorder').sortable('serialize')},
                    success: function(d, t, x) {
                        if (t == 1) {
                            $("#ajax-response").html('<div class="message updated fade"><p><?php _e('Items Order Updated', 'cpt') ?></p></div>');
                            $("#ajax-response div").delay(3000).hide("slow");
                        }
                    }
                });
            });
        });
    })(jQuery);
    </script>

    <?php
}

// Save the post order from POST data
add_action('wp_ajax_aka-postorder-tax-save-order', 'aka_postorder_taxonomy_save_order');
function aka_postorder_taxonomy_save_order() {
    $term = $_POST['term'];
    $holder = array();
    parse_str($_POST['order'], $holder);
    if (!$term || !is_numeric($term)) exit;

    foreach ($holder['order'] as $order => $post) {
        error_log("updating post $post with term $term and order $order");
        update_post_meta($post, '_tax_order_'.$term, $order);
    }

    exit;
}

add_action('set_object_terms', 'aka_postorder_taxonomy_add_terms', 10, 6);
function aka_postorder_taxonomy_add_terms($object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids) {
    global $wpdb;
    foreach ($terms as $term) {
        $order_name = "_tax_order_".$term;
        $order_var = get_post_meta($object_id, $order_name, true);
        if ($order_var === '') {
            $query = "SELECT max(meta_value) from {$wpdb->postmeta} WHERE meta_key='$order_name' AND post_id=$object_id";
            $highest = $wpdb->get_var("SELECT max(meta_value) from {$wpdb->postmeta} WHERE meta_key='$order_name'");
            if (is_null($highest)) {
                update_post_meta($object_id, $order_name, 0);
            } else {
                update_post_meta($object_id, $order_name, $highest+1);
            }
        }
    }

}


// Scripts and styles
add_action('admin_enqueue_scripts', 'aka_postorder_taxonomy_order_enqueue_scripts');
function aka_postorder_taxonomy_order_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-sortable');
    wp_register_style('postorder_order', plugins_url("/css/order-admin.css", __FILE__));
    wp_enqueue_style('postorder_order');
}

