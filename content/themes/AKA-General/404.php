<?php
/**
 * 404 Template file to output error meassage.
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

$context = Timber::get_context();
$context['posts'] = Timber::get_posts();
$templates = array( '404.twig' );
Timber::render( $templates, $context );
