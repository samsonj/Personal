<?php
/**
 * The standard page template file
 *
 */

// If external link, redirect
$type = get_field('type');
if($type == 'external') {
	wp_redirect(get_field('external_link'), 301);
}

$context = Timber::get_context();
$context['posts'] = Timber::get_posts();

$templates = array( 'single.twig' );

Timber::render( $templates, $context );
