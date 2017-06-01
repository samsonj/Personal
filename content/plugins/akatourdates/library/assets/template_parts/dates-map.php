<?php

// TOUR DATES -> LIST ON A MAP

if (post_type_exists( 'tourdate' )) {
	wp_enqueue_script('akatourdates', AKATOURDATES_PLUGIN_URL.'library/scripts/akatourdates.js', array('jquery'));
	$loop = new WP_Query(array('post_type' => 'tourdate', 'meta_key' => 'tourdate_enddate', 'orderby' => 'meta_value', 'order' => 'ASC', 'posts_per_page' => -1));
	if($loop->have_posts()) {
		?>
			<div class="tourDates map">
				<?php $tourdate_map = get_option('tourdate_map'); if(empty($tourdate_map)) { $tourdate_map = AKATOURDATES_PLUGIN_URL.'/library/images/map.png'; } ?>
        		<img class="mapImage" src="<?php echo $tourdate_map; ?>" alt="Country Map">
	    		<ul>
				<?php
				while($loop->have_posts()) {
					$loop->the_post();
					$custom = get_post_meta(get_the_ID());
					$date_format = "d/m/Y";
					$start_date = explode('-', $custom['tourdate_startdate'][0]);
					$end_date = explode('-', $custom['tourdate_enddate'][0]);
					$dates = date($date_format, mktime(0, 0, 0, $start_date[1], $start_date[2], $start_date[0])).' - '.date($date_format, mktime(0, 0, 0, $end_date[1], $end_date[2], $end_date[0]));
					$coord_x = 0; $coord_y = 0;
					if ($custom['tourdate_location'][0]) {
               			global $wpdb;
               			$location = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."tourdatelocation WHERE id = ".$custom['tourdate_location'][0]);
    		   			$coord = json_decode($location->coordinates);
    		   			$coord_x = $coord->x;
    		   			$coord_y = $coord->y;
                	}
					?>
					<li data-x='<?php echo $coord_x; ?>' data-y='<?php echo $coord_y; ?>'>
						<div class="marker">.</div>
						<div class="details">
							<div class="location">
								<?php if($custom['tourdate_venuelink'][0]) { ?><a href="<?php echo $custom['tourdate_venuelink'][0]; ?>" target="_blank" rel="nofollow"><?php } ?>
								<?php echo $custom['tourdate_venuename'][0]; ?>
								<?php if($custom['tourdate_venuelink'][0]) { ?></a><?php } ?>
							</div>
							<div class="dates"><?php echo $dates; ?></div>
							<div class="booking">
								<?php 
								if($custom['tourdate_onsale'][0] == '1') {
									if($custom['tourdate_bookinglink'][0]) { ?><a href="<?php echo $custom['tourdate_bookinglink'][0]; ?>" class="btn" target="_blank" rel="nofollow">Book Tickets</a><?php }
									if($custom['tourdate_bookingnumber'][0]) { ?>or call <?php echo $custom['tourdate_bookingnumber'][0]; ?><?php }
								} else {
									echo $custom['tourdate_notonsalemessage'][0];
								}
								?>
							</div>
							<span class="close">x</span>
						</div>
					</li>
					<?php
				}
				?>
			</ul>
		</div>
		<?php
	}    
	wp_reset_postdata();
}
?>