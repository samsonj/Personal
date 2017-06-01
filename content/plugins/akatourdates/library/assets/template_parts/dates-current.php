<?php

// TOUR DATES LISTED IN A HTML LIST MARKUP

if (post_type_exists( 'tourdate' )) {
	echo do_shortcode("
		[currentlocation 
			date_format='d/m/Y'
			output='
				<h2>Currently playing at %name%</h2>
				<p>%open_booking_link%Book online%close_link% or call %booking_number%</p>
			'
			nowhere_output='
				<h2>Next performance at %name% from %start_date%</h2>
				<p>%open_booking_link%Book online%close_link% or call %booking_number%</p>
			'
			tourend_output='
				<h2>The tour is now finished!</h2>
			'
		]
	");
}
?>