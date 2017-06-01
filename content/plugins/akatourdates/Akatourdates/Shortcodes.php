<?php
/**
 * Aka Tour Dates Shortcodes class
 */
class Akatourdates_Shortcodes {

    public static
    $instance = null;


    /**
     * Object constructor
     */
    function __construct() {
        self::$instance = $this;

        // Add the javascript
        add_action( 'wp_enqueue_scripts', array($this, 'include_js_css'));

        // Add shortcodes
        add_shortcode('akatourdates', array($this, 'display_tour_dates'));
        add_shortcode('tourdates', array($this, 'display_tour_dates'));
        add_shortcode('akacurrentlocation', array($this, 'display_current_location'));
        add_shortcode('currentlocation', array($this, 'display_current_location'));
    }

    /**
    * Include JS
    **/
    public function include_js_css() {
    	wp_enqueue_style('akatourdates', AKATOURDATES_PLUGIN_URL.'library/styles/akatourdates.css');
    }

    /**
    * Display Tour Dates
    */
    public function display_tour_dates($atts) {
    	wp_enqueue_script('akatourdates', AKATOURDATES_PLUGIN_URL.'library/scripts/akatourdates.js', array('jquery'));

        global $post, $wpdb;
        $table_name = $wpdb->prefix."tourdatelocation";

        ob_start();
        extract(shortcode_atts( array(
			'view'				=> 'list',
			'output_order'		=> 'dlb',
			'date_format'		=> 'j M Y',
			'dates_tag'			=> 'div',
			'location_format'	=> '%open_venue_link%%name%%close_link% - %city%',
			'location_tag'		=> 'div',
			'booking_format'	=> '%open_booking_link%Book online%close_link% or call %booking_number%',
			'booking_tag'		=> 'div',
            'show_past_dates'   => 1,
			'unique_id'		=> ''
		), $atts));

		$location_format = html_entity_decode($location_format);
		$booking_format = html_entity_decode($booking_format);

		$tourdates = array();
		$args = array('post_type' => 'tourdate', 'meta_key' => 'tourdate_enddate', 'orderby' => 'meta_value', 'order' => 'ASC', 'posts_per_page' => -1);
        if(!$show_past_dates || $show_past_dates == 0) {
            $args['meta_query'] = array(array(
                'key' => 'tourdate_enddate',
                'compare' => '>=',
                'value' => date('Y-m-d', mktime())
            ));
        }
        $loop = new WP_Query($args);
        if($loop->have_posts()) {
            while($loop->have_posts()) {
            	$loop->the_post();
            	$tourdates[] = $post;
            }
        }

        // If no view specified, list by default
        if(!isset($view) || empty($view)) { $view = 'list'; }

        // For each tour date, generating a table with all the needed informations
        $tourdate_list = array(); $i = 0;
        if(!empty($tourdates)) {
           foreach($tourdates as $tourdate) {
           		$custom = get_post_meta($tourdate->ID);
                $permalink = get_permalink(get_the_ID());
                if ($custom['tourdate_location'][0]) {
               		$location = $wpdb->get_row("SELECT * FROM $table_name WHERE id = ".$custom['tourdate_location'][0]);
    		   		$coord = json_decode($location->coordinates);
    		   		$tourdate_list[$i]['coord_x'] = $coord->x;
    		   		$tourdate_list[$i]['coord_y'] = $coord->y;
                }
         		$start_date = explode('-', $custom['tourdate_startdate'][0]);
				$end_date = explode('-', $custom['tourdate_enddate'][0]);
                if(!empty($custom['tourdate_startdate'][0]))
                    $tourdate_list[$i]['dates'] = date($date_format, mktime(0, 0, 0, $start_date[1], $start_date[2], $start_date[0])) . ' - ';
                $tourdate_list[$i]['dates'] .= date($date_format, mktime(0, 0, 0, $end_date[1], $end_date[2], $end_date[0]));
				$tags = array('%title%', '%city%', '%country%', '%name%', '%address1%', '%address2%', '%post_code%', '%open_venue_link%', '%open_booking_link%', '%open_permalink%', '%close_link%', '%venue_link%', '%booking_link%', '%permalink%', '%booking_number%');
				$values = array($tourdate->post_title, $custom['tourdate_venuecity'][0], $custom['tourdate_venuecountry'][0], $custom['tourdate_venuename'][0], $custom['tourdate_venueaddress1'][0], $custom['tourdate_venueaddress2'][0], $custom['tourdate_venuepostcode'][0], '<a href="'.$custom['tourdate_venuelink'][0].'" target="_blank">', '<a class="tourDateBtn btn" href="'.$custom['tourdate_bookinglink'][0].'" target="_blank" rel="nofollow">', '<a class="btn" href="'.$permalink.'">', '</a>', $custom['tourdate_venuelink'][0], $custom['tourdate_bookinglink'][0], $permalink, $custom['tourdate_bookingnumber'][0]);
				$tourdate_list[$i]['location'] = str_replace($tags, $values, $location_format);
				$tourdate_list[$i]['location'] = str_replace('href=""', '', $tourdate_list[$i]['location']);
				$tourdate_list[$i]['box_office'] = $custom['tourdate_notonsalemessage'][0];
				$tourdate_list[$i]['box_office'] = str_replace('href=""', '', $tourdate_list[$i]['box_office']);
				if($custom['tourdate_onsale'][0] == '1') {
					$tourdate_list[$i]['box_office'] = str_replace($tags, $values, $booking_format);
				}
				$tourdate_list[$i]['css_classes'] = $custom['tourdate_cssclasses'][0];
				$tourdate_list[$i]['tourdate_onmap'] = $custom['tourdate_onmap'][0];
				$i++;
         	}
        }

        // Init for the output order
        $output_order = array(substr($output_order, 0, 1), substr($output_order, 1, 1), substr($output_order, 2, 1));

        switch($view) {

        	case 'list':
        		// Display the tour dates list
        		?>
        		<div class="tourDates list" <?php if(isset($unique_id) && !empty($unique_id)) { echo 'id="'.$unique_id.'"'; } ?>>
        			<ul>
        				<?php
        				foreach($tourdate_list as $tourdate) {
        					?><li <?php if(isset($tourdate['css_classes']) && !empty($tourdate['css_classes'])) { echo 'class="'.$tourdate['css_classes'].'"'; } ?>><?php
        					foreach($output_order as $info) {
        						switch($info) {
        							case 'd':
										?><<?php echo $dates_tag; ?> class="dates"><?php echo $tourdate['dates']; ?></<?php echo $dates_tag; ?>><?php
    	    							break;
    	    						case 'l':
    	    							?><<?php echo $location_tag; ?> class="location"><?php echo $tourdate['location']; ?></<?php echo $location_tag; ?>><?php
    	    							break;
    	    						case 'b':
    	    							?><<?php echo $booking_tag; ?> class="booking"><?php echo $tourdate['box_office']; ?></<?php echo $booking_tag; ?>><?php
    	    							break;
        						}
        					}
        					?></li><?php
        				}
        				?>
        			</ul>
        		</div>
        		<?php
        		break;

        	case 'table':
        		// Display the tour dates table
        		?>
        		<table class="tourDates table" <?php if(isset($unique_id) && !empty($unique_id)) { echo 'id="'.$unique_id.'"'; } ?>>
        			<thead>
        				<tr>
        				<?php
        				foreach($output_order as $info) {
        				    switch($info) {
    	    			    	case 'd':
						    		?><th><<?php echo $dates_tag; ?> class="date">Dates</<?php echo $dates_tag; ?>></th><?php
    	    			    		break;
    	    			    	case 'l':
    	    			    		?><th><<?php echo $location_tag; ?> class="location">Location</<?php echo $location_tag; ?>></th><?php
    	    			    		break;
    	    			    	case 'b':
    	    			    		?><th><<?php echo $booking_tag; ?> class="booking">Box Office</<?php echo $booking_tag; ?>></th><?php
    	    			    		break;
	        			    }
        				}
        				?>
        				</tr>
        			</thead>
        			<tbody>
        				<?php
        				foreach($tourdate_list as $tourdate) {
        					?><tr <?php if(isset($tourdate['css_classes']) && !empty($tourdate['css_classes'])) { echo 'class="'.$tourdate['css_classes'].'"'; } ?>><?php
        					foreach($output_order as $info) {
        						switch($info) {
        							case 'd':
										?><td><<?php echo $dates_tag; ?> class="dates"><?php echo $tourdate['dates']; ?></<?php echo $dates_tag; ?>></td><?php
    	    							break;
    	    						case 'l':
    	    							?><td><<?php echo $location_tag; ?> class="location"><?php echo $tourdate['location']; ?></<?php echo $location_tag; ?>></td><?php
    	    							break;
    	    						case 'b':
    	    							?><td><<?php echo $booking_tag; ?> class="booking"><?php echo $tourdate['box_office']; ?></<?php echo $booking_tag; ?>></td><?php
    	    							break;
        						}
        					}
        					?></tr><?php
        				}
        				?>
        			</tbody>
        		</table>
        		<?php
        		break;

        	case 'map':
        		// Display the tour dates map
        		?>
        		<div class="tourDates map" <?php if(isset($unique_id) && !empty($unique_id)) { echo 'id="'.$unique_id.'"'; } ?>>
        			<?php $tourdate_map = get_option('tourdate_map'); if(empty($tourdate_map)) { $tourdate_map = AKATOURDATES_PLUGIN_URL.'/library/images/map.png'; } ?>
        			<img class="mapImage" src="<?php echo $tourdate_map; ?>" alt="Country Map">
        			<ul>
        				<?php
        				foreach($tourdate_list as $tourdate) {
        					if($tourdate['tourdate_onmap'] == 1) {
	        					?><li <?php if(isset($tourdate['css_classes']) && !empty($tourdate['css_classes'])) { echo 'class="'.$tourdate['css_classes'].'"'; } ?> data-x='<?php echo $tourdate['coord_x']; ?>' data-y='<?php echo $tourdate['coord_y']; ?>'><?php
		        					?><div class="marker">.</div><?php
		        					?><div class="details"><?php
		        					foreach($output_order as $info) {
	        							switch($info) {
	        								case 'd':
												?><<?php echo $dates_tag; ?> class="dates"><?php echo $tourdate['dates']; ?></<?php echo $dates_tag; ?>><?php
	    	    								break;
	    	    							case 'l':
	    	    								?><<?php echo $location_tag; ?> class="location"><?php echo $tourdate['location']; ?></<?php echo $location_tag; ?>><?php
	    	    								break;
	    	    							case 'b':
	    	    								?><<?php echo $booking_tag; ?> class="booking"><?php echo $tourdate['box_office']; ?></<?php echo $booking_tag; ?>><?php
	    	    								break;
										}
									}
									?><span class="close">x</span>
									</div><?php
								?></li><?php
        					}
        				}
        				?>
        			</ul>
        		</div>
        		<?php
        		break;

        }
        wp_reset_postdata();
        return ob_get_clean();
    }

    /**
    * Display Current Location
    */
    public function display_current_location($atts) {
        global $post, $wpdb;
        $table_name = $wpdb->prefix."tourdatelocation";

        ob_start();
        extract(shortcode_atts( array(
			'date_format'		=> 'j M Y',
			'output'			=> '<h2>Currently playing at %name%</h2><p>%open_booking_link%Book online%close_link% or call %booking_number%</p>',
			'nowhere_output'	=> '<h2>Next performance at %name% from %start_date%</h2><p>%open_booking_link%Book online%close_link% or call %booking_number%</p>',
			'tourend_output'	=> '<h2>The tour is now finished!</h2>'
		), $atts));

		$output = html_entity_decode($output);
		$nowhere_output = html_entity_decode($nowhere_output);
		$tourend_output = html_entity_decode($tourend_output);

		$tags = array('%start_date%', '%end_date%', '%title%', '%city%', '%country%', '%name%', '%address1%', '%address2%', '%post_code%', '%open_venue_link%', '%open_booking_link%', '%open_permalink%', '%close_link%', '%venue_link%', '%booking_link%', '%permalink%', '%booking_number%');

		// Querying the current tour date
		$loop = new WP_Query(array(
		    'post_type' => 'tourdate',
		    'posts_per_page' => 1,
		    'meta_key' => 'tourdate_enddate',
		    'orderby' => 'meta_value',
		    'order' => 'ASC',
		    'meta_query' => array(
   		     array(
   		        'key' => 'tourdate_startdate',
   		        'value' => date('Y-m-d', mktime()),
		    	'compare' => '<='
   		     ),
   		     array(
   		        'key' => 'tourdate_enddate',
   		        'value' => date('Y-m-d', mktime()),
		    	'compare' => '>='
   		     ))
		));

		if($loop->have_posts()) {
			$loop->the_post();
            $permalink = get_permalink(get_the_ID());
			$custom = get_post_meta($post->ID);
			$start_mktime = explode('-', $custom['tourdate_startdate'][0]);
			$start_mktime = mktime(0, 0, 0, $start_mktime[1], $start_mktime[2], $start_mktime[0]);
			$end_mktime = explode('-', $custom['tourdate_enddate'][0]);
			$end_mktime = mktime(0, 0, 0, $end_mktime[1], $end_mktime[2], $end_mktime[0]);
			$values = array(date($date_format, $start_mktime), date($date_format, $end_mktime), $post->post_title, $custom['tourdate_venuecity'][0], $custom['tourdate_venuecountry'][0], $custom['tourdate_venuename'][0], $custom['tourdate_venueaddress1'][0], $custom['tourdate_venueaddress2'][0], $custom['tourdate_venuepostcode'][0], '<a href="'.$custom['tourdate_venuelink'][0].'" target="_blank">', '<a class="btn" href="'.$custom['tourdate_bookinglink'][0].'" target="_blank" rel="nofollow">', '<a class="btn" href="'.$permalink.'">', '</a>', $custom['tourdate_venuelink'][0], $custom['tourdate_bookinglink'][0], $permalink, $custom['tourdate_bookingnumber'][0]);
			echo str_replace($tags, $values, $output);
		} else {
			$loop2 = new WP_Query(array(
			    'post_type' => 'tourdate',
			    'posts_per_page' => 1,
			    'meta_key' => 'tourdate_enddate',
		   		'orderby' => 'meta_value',
		    	'order' => 'ASC',
		    	'meta_query' => array(
   			     array(
   			        'key' => 'tourdate_startdate',
   			        'value' => date('Y-m-d', mktime()),
			    	'compare' => '>'
   			     ))
			));
			if($loop2->have_posts()) {
				$loop2->the_post();
                $permalink = get_permalink(get_the_ID());
				$custom = get_post_meta($post->ID);
				$start_mktime = explode('-', $custom['tourdate_startdate'][0]);
				$start_mktime = mktime(0, 0, 0, $start_mktime[1], $start_mktime[2], $start_mktime[0]);
				$end_mktime = explode('-', $custom['tourdate_enddate'][0]);
				$end_mktime = mktime(0, 0, 0, $end_mktime[1], $end_mktime[2], $end_mktime[0]);
				$values = array(date($date_format, $start_mktime), date($date_format, $end_mktime), $post->post_title, $custom['tourdate_venuecity'][0], $custom['tourdate_venuecountry'][0], $custom['tourdate_venuename'][0], $custom['tourdate_venueaddress1'][0], $custom['tourdate_venueaddress2'][0], $custom['tourdate_venuepostcode'][0], '<a href="'.$custom['tourdate_venuelink'][0].'" target="_blank">', '<a class="btn" href="'.$custom['tourdate_bookinglink'][0].'" target="_blank" rel="nofollow">', '<a class="btn" href="'.$permalink.'">', '</a>', $custom['tourdate_venuelink'][0], $custom['tourdate_bookinglink'][0], $permalink, $custom['tourdate_bookingnumber'][0]);
				echo str_replace($tags, $values, $nowhere_output);
			} else {
				echo $tourend_output;
			}
		}
		wp_reset_postdata();
        return ob_get_clean();

    }

}
