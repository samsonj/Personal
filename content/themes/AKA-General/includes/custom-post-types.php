<?php

// Cast & Creative 

add_action( 'init', 'cast_creative_posts' );

function cast_creative_posts() {
        $labels = array(
                'name'                  => _x( 'Cast', 'Post Type General Name', 'text_domain' ),
                'singular_name'         => _x( 'Cast', 'Post Type Singular Name', 'text_domain' ),
                'menu_name'             => __( 'Cast', 'text_domain' ),
                'name_admin_bar'        => __( 'Cast', 'text_domain' ),
                'parent_item_colon'     => __( 'Cast Member:', 'text_domain' ),
                'all_items'             => __( 'All Cast Members', 'text_domain' ),
                'add_new_item'          => __( 'Add New Cast Member', 'text_domain' ),
                'add_new'               => __( 'Add New', 'text_domain' ),
                'new_item'              => __( 'New Cast Member', 'text_domain' ),
                'edit_item'             => __( 'Edit Cast Member', 'text_domain' ),
                'update_item'           => __( 'Update Cast Member', 'text_domain' ),
                'view_item'             => __( 'View Cast Member', 'text_domain' ),
                'search_items'          => __( 'Search Cast Members', 'text_domain' ),
                'not_found'             => __( 'Not found', 'text_domain' ),
                'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
                'items_list'            => __( 'Cast Members list', 'text_domain' ),
                'items_list_navigation' => __( 'Cast Members list navigation', 'text_domain' ),
                'filter_items_list'     => __( 'Filter Cast Members', 'text_domain' ),
        );
        $args = array(
                'label'                 => __( 'Cast', 'text_domain' ),
                'description'           => __( 'Cast Custom Post', 'text_domain' ),
                'labels'                => $labels,
                'supports'                    => array( 'title', 'editor', 'revisions'),
                'taxonomies'            => array( ),
                'hierarchical'          => false,
                'public'                => true,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'menu_position'         => 20,
                'menu_icon'             => 'dashicons-universal-access',
                'show_in_admin_bar'     => true,
                'show_in_nav_menus'     => true,
                'can_export'            => true,
                'has_archive'           => false,
                'exclude_from_search'   => false,
                'publicly_queryable'    => true,
                'capability_type'       => 'page',
        );
        register_post_type( 'cast', $args );

        $labels = array(
                'name'                  => _x( 'Creative', 'Post Type General Name', 'text_domain' ),
                'singular_name'         => _x( 'Creative', 'Post Type Singular Name', 'text_domain' ),
                'menu_name'             => __( 'Creative', 'text_domain' ),
                'name_admin_bar'        => __( 'Creative', 'text_domain' ),
                'parent_item_colon'     => __( 'Creative Member:', 'text_domain' ),
                'all_items'             => __( 'All Creative Members', 'text_domain' ),
                'add_new_item'          => __( 'Add New Creative Member', 'text_domain' ),
                'add_new'               => __( 'Add New', 'text_domain' ),
                'new_item'              => __( 'New Creative Member', 'text_domain' ),
                'edit_item'             => __( 'Edit Creative Member', 'text_domain' ),
                'update_item'           => __( 'Update Creative Member', 'text_domain' ),
                'view_item'             => __( 'View Creative Member', 'text_domain' ),
                'search_items'          => __( 'Search Creative Members', 'text_domain' ),
                'not_found'             => __( 'Not found', 'text_domain' ),
                'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
                'items_list'            => __( 'Creative Members list', 'text_domain' ),
                'items_list_navigation' => __( 'Creative Members list navigation', 'text_domain' ),
                'filter_items_list'     => __( 'Filter Creative Members', 'text_domain' ),
        );
        $args = array(
                'label'                 => __( 'Creative', 'text_domain' ),
                'description'           => __( 'Creative Custom Post', 'text_domain' ),
                'labels'                => $labels,
                'supports'                    => array( 'title', 'editor', 'revisions'),
                'taxonomies'            => array( ),
                'hierarchical'          => false,
                'public'                => true,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'menu_position'         => 20,
                'menu_icon'             => 'dashicons-businessman',
                'show_in_admin_bar'     => true,
                'show_in_nav_menus'     => true,
                'can_export'            => true,
                'has_archive'           => false,
                'exclude_from_search'   => false,
                'publicly_queryable'    => true,
                'capability_type'       => 'page',
        );
        register_post_type( 'creative', $args );
}


add_action( 'init', 'cast_creative_taxonomy', 0 );
function cast_creative_taxonomy() {
        $labels = array(
                'name'              => _x( 'Groups', 'taxonomy general name' ),
                'singular_name'     => _x( 'Group', 'taxonomy singular name' ),
                'search_items'      => __( 'Search Groups' ),
                'all_items'         => __( 'All Groups' ),
                'edit_item'         => __( 'Edit Group' ),
                'update_item'       => __( 'Update Group' ),
                'add_new_item'      => __( 'Add New Group' ),
                'new_item_name'     => __( 'New Group' ),
                'menu_name'         => __( 'Groups' ),
        );

        $args = array(
                'labels' => $labels,
                'hierarchical' => true,
                'public'        => true,
                'has_archive'   => false,
        );

        register_taxonomy( 'cast_group', 'cast', $args );
        register_taxonomy( 'creative_group', 'creative', $args );
}



// Gallery

function gallery_post_type() {

        $labels = array(
                'name'                  => _x( 'Galleries', 'Post Type General Name', 'text_domain' ),
                'singular_name'         => _x( 'Gallery', 'Post Type Singular Name', 'text_domain' ),
                'menu_name'             => __( 'Galleries', 'text_domain' ),
                'name_admin_bar'        => __( 'Galleries', 'text_domain' ),
                'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
                'all_items'             => __( 'All Galleries', 'text_domain' ),
                'add_new_item'          => __( 'Add New Gallery', 'text_domain' ),
                'add_new'               => __( 'Add New', 'text_domain' ),
                'new_item'              => __( 'New Item', 'text_domain' ),
                'edit_item'             => __( 'Edit Item', 'text_domain' ),
                'update_item'           => __( 'Update Item', 'text_domain' ),
                'view_item'             => __( 'View Item', 'text_domain' ),
                'search_items'          => __( 'Search Item', 'text_domain' ),
                'not_found'             => __( 'Not found', 'text_domain' ),
                'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
                'items_list'            => __( 'Items list', 'text_domain' ),
                'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
                'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
        );
        $args = array(
                'label'                 => __( 'Galleries', 'text_domain' ),
                'description'           => __( 'Galleries Custom Post', 'text_domain' ),
                'labels'                => $labels,
                'supports'                      => array( 'title', 'editor', 'revisions'),
                'taxonomies'            => array( ),
                'hierarchical'          => false,
                'public'                => true,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'menu_position'         => 20,
                'menu_icon'             => 'dashicons-images-alt',
                'show_in_admin_bar'     => true,
                'show_in_nav_menus'     => true,
                'can_export'            => true,
                'has_archive'           => false,
                'exclude_from_search'   => false,
                'publicly_queryable'    => true,
                'capability_type'       => 'page',
        );
        register_post_type( 'gallery', $args );

}
add_action( 'init', 'gallery_post_type', 0 );

function gallery_taxonomy() {
  $labels = array(
    'name'              => _x( 'Gallery Types', 'taxonomy general name' ),
    'singular_name'     => _x( 'Gallery Types', 'taxonomy singular name' ),
    'search_items'      => __( 'Search Gallery Types' ),
    'all_items'         => __( 'All Gallery Types' ),
    'parent_item'       => __( 'Parent Gallery Type' ),
    'parent_item_colon' => __( 'Parent Gallery Type:' ),
    'edit_item'         => __( 'Edit Gallery Type' ),
    'update_item'       => __( 'Update Gallery Type' ),
    'add_new_item'      => __( 'Add New Gallery Type' ),
    'new_item_name'     => __( 'New Gallery Type' ),
    'menu_name'         => __( 'Gallery Type' ),
  );

  $args = array(
    'labels' => $labels,
    'hierarchical' => true,
    'public'        => true,
    'has_archive'   => true,
  );

  register_taxonomy( 'galleries_category', 'galleries', $args );
}
add_action( 'init', 'gallery_taxonomy', 0 );

