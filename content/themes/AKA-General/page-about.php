<?php
/*
	Template Name: About the show
*/

$context = Timber::get_context();
$context['posts'] = Timber::get_posts();
$templates = array( 'about.twig' );

Timber::render( $templates, $context );
