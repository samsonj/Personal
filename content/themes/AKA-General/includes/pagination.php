<?php

function get_pagination($all_posts, $posts_per_page, $paged, $current_URL) {
	$pagination = array();
	$pagination['pages'] = array();
	$nb_posts = sizeof($all_posts);
	if($nb_posts > $posts_per_page) {
	    $nb_pages = ceil($nb_posts / $posts_per_page);
	    for($i = 1 ; $i <= $nb_pages ; $i++) {
	        $pagination_item = array();
	        $pagination_item['label'] = $i;
	        $pagination_item['link'] = $current_URL.'page/'.$i;
	        $pagination_item['current'] = ($paged == $i) ? true : false;
	        $pagination['pages'][] = $pagination_item;
	    }
	    if($paged < $nb_pages) {
	        $pagination['next'] = $current_URL.'page/'.($paged + 1);
	    }
	    if($paged > 1) {
	        $pagination['prev'] = $current_URL.'page/'.($paged - 1);
	    }
	}
	return $pagination;
}
