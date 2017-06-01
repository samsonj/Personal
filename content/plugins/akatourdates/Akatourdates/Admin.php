<?php
/**
* Aka Tour Dates admin page class
*/
class Akatourdates_Admin {

    public static
    $instance = null;

    /**
    * Object constructor
    */
    function __construct() {
        self::$instance = $this;

		// Add javascript and CSS
		add_action( 'admin_enqueue_scripts',  array($this, 'include_js'));

        // Initialisation
        add_action('admin_init', array($this, 'admin_init'));

        // Create standard meta box for Establishments
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));

        // Save entry metas
        add_action('save_post', array($this, 'save_tourdate'), 10, 2);

        // Add ajax action for deleting an entry image
        add_action('wp_ajax_akacomp_delete_entry_image', array($this, 'ajax_delete_entry_image'));
        add_action('wp_ajax_nopriv_akacomp_delete_entry_image', array($this, 'ajax_delete_entry_image'));

		// Add option page
		add_action('admin_menu', array($this, 'change_admin_menu'));
		
		// Add admin notice
		add_action( 'admin_notices', array($this, 'display_admin_notice'));
		
    }

    /**
    * Wordpress admin_init
    */
    public function admin_init() {
		wp_enqueue_style('jquery.ui', 'http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css');
		wp_enqueue_style('akatourdates-admin', AKATOURDATES_PLUGIN_URL.'/library/styles/akatourdates-admin.css');
		global $current_user;
		$user_id = $current_user->ID;
		if ( isset($_GET['tourdates_ignore_template_parts_notice']) && '0' == $_GET['tourdates_ignore_template_parts_notice'] ) {
			add_user_meta($user_id, 'tourdates_ignore_template_parts_notice', 'true', true);
		}
    }

    /**
    * Include JS
    **/
    public function include_js() {
    	// wp_enqueue_script('jquery.ui', 'http://code.jquery.com/ui/1.10.3/jquery-ui.js', array('jquery'));
      wp_enqueue_script('jquery-ui-datepicker');
      wp_enqueue_script('jquery-effects-blind');
    	wp_enqueue_script('akatourdates-admin', AKATOURDATES_PLUGIN_URL.'/library/scripts/akatourdates-admin.js', array('jquery'));
    	wp_enqueue_media();
    }

    /**
    * META BOXES
    */
    public function add_meta_boxes()
    {
    	add_meta_box('tourdate-dates-meta', 'Dates', array($this, 'create_tourdate_dates_metabox'), 'tourdate', 'normal');
    	add_meta_box('tourdate-status-meta', 'Status', array($this, 'create_tourdate_status_metabox'), 'tourdate', 'normal');
		add_meta_box('tourdate-venue-meta', 'Venue Details', array($this, 'create_tourdate_venue_metabox'), 'tourdate', 'normal');
		if(is_plugin_active('akanextshow/akanextshow.php')) {
			// Add the performance details used by the next show plugin only if it is activated
			add_meta_box('tourdate-performance-meta', 'Performance Details (only used by AKA Next Show plugin)', array($this, 'create_tourdate_performance_metabox'), 'tourdate', 'normal');
		}
		add_meta_box('tourdate-advanced-meta', 'Advanced', array($this, 'create_tourdate_advanced_metabox'), 'tourdate', 'normal');
    }

    /**
    * Create standard meta box for Tour Date Dates
    */
    public function create_tourdate_dates_metabox()
    {
        global $post, $wpdb;
        if ($post->post_type != 'tourdate') return;
        $data = array_map('reset', get_post_custom($post->ID));
        wp_nonce_field('tourdate_metas', 'tourdate_metas');
        ?>
        <script type="text/javascript">
        	var plugin_url = '<?php echo AKATOURDATES_PLUGIN_URL; ?>';
        </script>
        <table class="form-table posttype">

            <tr>
                <th scope="row"><label for="tourdate_startdate">From</label></th>
                <td><input class="datePicker" type="text" id="tourdate_startdate" name="tourdate_startdate" value="<?php echo esc_attr($data['tourdate_startdate']); ?>" /></td>
            </tr>
            <tr>
                <th scope="row"><label for="tourdate_enddate">To</label></th>
                <td><input class="datePicker" type="text" id="tourdate_enddate" name="tourdate_enddate" value="<?php echo esc_attr($data['tourdate_enddate']); ?>" /></td>
            </tr>
            
            

        </table>
        <?php
    }
	
    /**
    * Create standard meta box for Tour Date Status
    */
    public function create_tourdate_status_metabox()
    {
        global $post, $wpdb;
        if ($post->post_type != 'tourdate') return;
        $data = array_map('reset', get_post_custom($post->ID));
        wp_nonce_field('tourdate_metas', 'tourdate_metas');
        ?>
        <table class="form-table posttype">

            <tr>
                <th scope="row"><label>On sale status</label></th>
                <td>
                	<input type="radio" id="tourdate_onsale" name="tourdate_onsale" <?php if(esc_attr($data['tourdate_onsale']) == '1') { echo "checked"; } ?> value="1" /><label for="tourdate_onsale"> On sale</label>
                	<br />
                	<input type="radio" id="tourdate_notonsale" name="tourdate_onsale" <?php if(esc_attr($data['tourdate_onsale']) !== '1') { echo "checked"; } ?> value="0" /><label for="tourdate_notonsale"> Not on Sale</label>
                </td>
            </tr>
            <tr id="tourdate_notonsale_tr">
                <th scope="row"><label for="tourdate_notonsalemessage">Message</label></th>
                <td><input type="text" id= "tourdate_notonsalemessage" name="tourdate_notonsalemessage" value="<?php echo esc_attr($data['tourdate_notonsalemessage']); ?>" /></td>
            </tr>
            <tbody id="tourdate_booking_details">
	            <tr>
           		    <th scope="row"><label for="tourdate_bookinglink">Booking link</label></th>
           		    <td><input type="text" id= "tourdate_bookinglink" name="tourdate_bookinglink" value="<?php echo esc_attr($data['tourdate_bookinglink']); ?>" /></td>
           		</tr>
		   		<tr>
           		    <th scope="row"><label for="tourdate_bookingnumber">Booking number</label></th>
           		    <td><input type="text" id= "tourdate_bookingnumber" name="tourdate_bookingnumber" value="<?php echo esc_attr($data['tourdate_bookingnumber']); ?>" /></td>
           		</tr>
            </tbody>
            

        </table>
        <?php
    }
    
    /**
    * Create standard meta box for Tour Date Venue details
    */
    public function create_tourdate_venue_metabox()
    {
        global $post, $wpdb;
        if ($post->post_type != 'tourdate') return;
        $data = array_map('reset', get_post_custom($post->ID));
        wp_nonce_field('tourdate_metas', 'tourdate_metas');
        ?>
        <table class="form-table posttype">

            <tr>
                <th scope="row"><label for="tourdate_venuename">Name</label></th>
                <td><input type="text" id="tourdate_venuename" name="tourdate_venuename" value="<?php echo esc_attr($data['tourdate_venuename']); ?>" /></td>
            </tr>
            <tr>
                <th scope="row"><label for="tourdate_venueaddress1">Address 1</label></th>
                <td><input type="text" id="tourdate_venueaddress1" name="tourdate_venueaddress1" value="<?php echo esc_attr($data['tourdate_venueaddress1']); ?>" /></td>
            </tr>
            <tr>
                <th scope="row"><label for="tourdate_venueaddress2">Address 2</label></th>
                <td><input type="text" id="tourdate_venueaddress2" name="tourdate_venueaddress2" value="<?php echo esc_attr($data['tourdate_venueaddress2']); ?>" /></td>
            </tr>
            <tr>
                <th scope="row"><label for="tourdate_venuecity">City</label></th>
                <td><input type="text" id="tourdate_venuecity" name="tourdate_venuecity" value="<?php echo esc_attr($data['tourdate_venuecity']); ?>" /></td>
            </tr>
            <tr>
                <th scope="row"><label for="tourdate_venuepostcode">Postcode</label></th>
                <td><input type="text" id="tourdate_venuepostcode" name="tourdate_venuepostcode" value="<?php echo esc_attr($data['tourdate_venuepostcode']); ?>" /></td>
            </tr>
            <tr>
                <th scope="row"><label for="tourdate_venuecountry">Country</label></th>
                <td>
                	<select id="tourdate_venuecountry" name="tourdate_venuecountry">
                		<option>-- Select a country</option>
                		<?php
                		$main_countries = array("Australia", "Austria", "Belgium", "Canada", "China", "Czech Republic", "Denmark", "France", "Finland", "Germany", "Hong Kong", "Hungary", "Iceland", "India", "Italy", "Ireland", "Netherlands", "New Zealand", "Norway", "Portugal", "Russia", "Spain", "Sweden", "Switzerland", "United Kingdom", "United States");
                		$countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus",  "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Israel", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands Antilles", "New Caledonia", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland",  "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");
                		foreach($main_countries as $main_country) {
                			?><option value="<?php echo $main_country; ?>" <?php if($main_country == esc_attr($data['tourdate_venuecountry'])) { echo "selected"; } ?>><?php echo $main_country; ?></option><?php
                		}
                		?>
                	</select>
                </td>
            </tr>
            <tr id="tourdate_map">
                <th scope="row"><label for="tourdate_onmap">Show location on map</label></th>
                <td><input type="checkbox" id="tourdate_onmap" name="tourdate_onmap" <?php if(esc_attr($data['tourdate_onmap']) == 1) { echo "checked"; } ?> /></td>
            </tr>
            <tbody id="tourdate_map_details">
		   		<tr >
           		    <th scope="row"><label for="tourdate_location">Location</label></th>
           		    <td id="tourdate_location_td">
           		    	<div id="tourdate_location_select">
           		    		<select name="tourdate_location" id="tourdate_location">
           		    			<option value="">-- Choose a location</option>
           		    			<?php
           		    			$table_name = $wpdb->prefix."tourdatelocation";
           		    			$locations = $wpdb->get_results("SELECT * FROM $table_name ORDER BY name ASC;");
           		    			foreach($locations as $location) {
           		    				$coord = json_decode($location->coordinates);
           		    				?>
           		    				<option data-x="<?php echo $coord->x; ?>" data-y="<?php echo $coord->y; ?>" <?php if(esc_attr($data['tourdate_location']) == $location->id) { echo "selected"; } ?> value="<?php echo $location->id; ?>"><?php echo $location->name; ?></option>
           		    				<?php
           		    			}
           		    			?>
           		    		</select>
           		    		or <a href="#" class="add_location">add one</a>
           		    		<br />
           		    		<em>(You can edit the locations in <a href="<?php echo admin_url(); ?>edit.php?post_type=tourdate&page=tourdates-settings&tab=locations">Settings > Tour Dates</a>)</em>
           		    	</div>
           		    	<div id="tourdate_location_add">
           		    		1. <input type="text" name="tourdate_location_name" id="tourdate_location_name" data-defaultvalue="Enter the location name here…">
           		    		<input type="hidden" name="tourdate_location_x" id="tourdate_location_x">
           		    		<input type="hidden" name="tourdate_location_y" id="tourdate_location_y">
           		    		<br />
           		    		2. Click on the map below where the venue is located
           		    		<br />
           		    		<a href="#" class="select_location">Cancel and choose an existing location</a>
           		    	</div>
           		    </td>
           		</tr>
           		<tr>
           			<th scope="row">Marker</th>
           			<td>
           				<div id="tourdatemap">
           					<?php $tourdate_map = get_option('tourdate_map'); if(empty($tourdate_map)) { $tourdate_map = AKATOURDATES_PLUGIN_URL.'library/images/map.png'; } ?>
           					<img class="map" src="<?php echo $tourdate_map; ?>">
           					<img class="marker" src="<?php echo AKATOURDATES_PLUGIN_URL; ?>library/images/marker.png">
           				</div>
           				
           			</td>
           		</tr>
            </tbody>
            <tr>
            	<th scope="row"><label for="tourdate_venuelink">Link</label></th>
            	<td><input type="text" id="tourdate_venuelink" name="tourdate_venuelink" value="<?php echo esc_attr($data['tourdate_venuelink']); ?>" /></td>
            </tr>

        </table>
        <?php
    }

	
    /**
    * Create standard meta box for Tour Date Performance Details
    */
    public function create_tourdate_performance_metabox()
    {
        global $post, $wpdb;
        if ($post->post_type != 'tourdate') return;
        $data = array_map('reset', get_post_custom($post->ID));
        wp_nonce_field('tourdate_metas', 'tourdate_metas');
        ?>
        <table class="form-table posttype">
        
            <tr>
	    	    <td colspan="2">
	    	    	<em>
	    	    		Write the times with a dot between hours and minutes and either am or pm at the end. Separate each time by a space. Leave the field blank if there is no show on the relating day. Some examples: <strong>2pm 7.30pm</strong> or <strong>10am 6pm</strong> or <strong>8pm</strong>
	    	    	</em>
	    	    </td>
	    	</tr>
	    	<tr>
	    	    <th scope="row"><label>Monday</label></th>
		        <td><input type="text" name="tourdate_nextshow_day_1" id="tourdate_nextshow_day_1" value="<?php echo $data['tourdate_nextshow_day_1']; ?>"></td>
		    </tr>
		    <tr>
	    	    <th scope="row"><label>Tuesday</label></th>
		        <td><input type="text" name="tourdate_nextshow_day_2" id="tourdate_nextshow_day_2" value="<?php echo $data['tourdate_nextshow_day_2']; ?>"></td>
		    </tr>
		    <tr>
	    	    <th scope="row"><label>Wednesday</label></th>
		        <td><input type="text" name="tourdate_nextshow_day_3" id="tourdate_nextshow_day_3" value="<?php echo $data['tourdate_nextshow_day_3']; ?>"></td>
		    </tr>
		    <tr>
	    	    <th scope="row"><label>Thursday</label></th>
		        <td><input type="text" name="tourdate_nextshow_day_4" id="tourdate_nextshow_day_4" value="<?php echo $data['tourdate_nextshow_day_4']; ?>"></td>
		    </tr>
		    <tr>
	    	    <th scope="row"><label>Friday</label></th>
		        <td><input type="text" name="tourdate_nextshow_day_5" id="tourdate_nextshow_day_5" value="<?php echo $data['tourdate_nextshow_day_5']; ?>"></td>
		    </tr>
		    <tr>
	    	    <th scope="row"><label>Saturday</label></th>
		        <td><input type="text" name="tourdate_nextshow_day_6" id="tourdate_nextshow_day_6" value="<?php echo $data['tourdate_nextshow_day_6']; ?>"></td>
		    </tr>
		    <tr>
	    	    <th scope="row"><label>Sunday</label></th>
		        <td><input type="text" name="tourdate_nextshow_day_7" id="tourdate_nextshow_day_7" value="<?php echo $data['tourdate_nextshow_day_7']; ?>"></td>
		    </tr>

        </table>
        <?php
    }
    
    /**
    * Create standard meta box for Tour Date Advanced
    */
    public function create_tourdate_advanced_metabox()
    {
        global $post, $wpdb;
        if ($post->post_type != 'tourdate') return;
        $data = array_map('reset', get_post_custom($post->ID));
        wp_nonce_field('tourdate_metas', 'tourdate_metas');
        ?>
        <table class="form-table posttype">

            <tr>
                <th scope="row"><label for="tourdate_cssclasses">CSS Classes</label></th>
                <td><input type="text" id="tourdate_cssclasses" name="tourdate_cssclasses" value="<?php echo esc_attr($data['tourdate_cssclasses']); ?>" /></td>
            </tr>          

        </table>
        <?php
    }    

    /**
    * Save the post meta content for an entry post
    */
    public function save_tourdate($post_id, $post)
    {
    	$input = ($_SERVER['REQUEST_METHOD'] == 'POST') ? $_POST : $_GET;
        if ($post->post_type != 'tourdate' || !wp_verify_nonce($_POST['tourdate_metas'], 'tourdate_metas')) return;
        if($input['tourdate_onmap'] && $input['tourdate_onmap'] == 'on') {
        	$input['tourdate_onmap'] = 1;
        } else {
        	$input['tourdate_onmap'] = 0;
        }
        if(isset($input['tourdate_location_x']) && $input['tourdate_location_x'] > 0 && isset($input['tourdate_location_y']) && $input['tourdate_location_y'] > 0) {
        	global $wpdb;
        	$table_name = $wpdb->prefix."tourdatelocation";
        	$coord = array('x' => $input['tourdate_location_x'], 'y' => $input['tourdate_location_y']);
        	$coord = json_encode($coord);
        	$wpdb->insert( 
				$table_name, 
				array( 
					'name' => $input['tourdate_location_name'], 
					'coordinates' => $coord
				)
			);
			$input['tourdate_location'] = $wpdb->insert_id;
        }
        $filtered_data = filter_var_array($input, array(
            'tourdate_cssclasses' => FILTER_SANITIZE_STRING,
            'tourdate_startdate' => FILTER_SANITIZE_STRING,
            'tourdate_enddate' => FILTER_SANITIZE_STRING,
            'tourdate_notonsalemessage' => FILTER_SANITIZE_STRING,
            'tourdate_bookinglink' => FILTER_SANITIZE_STRING,
            'tourdate_nextshow_day_1' => FILTER_SANITIZE_STRING,
            'tourdate_nextshow_day_2' => FILTER_SANITIZE_STRING,
            'tourdate_nextshow_day_3' => FILTER_SANITIZE_STRING,
            'tourdate_nextshow_day_4' => FILTER_SANITIZE_STRING,
            'tourdate_nextshow_day_5' => FILTER_SANITIZE_STRING,
            'tourdate_nextshow_day_6' => FILTER_SANITIZE_STRING,
            'tourdate_nextshow_day_7' => FILTER_SANITIZE_STRING,
            'tourdate_venuename' => FILTER_SANITIZE_STRING,
            'tourdate_venueaddress1' => FILTER_SANITIZE_STRING,
            'tourdate_venueaddress2' => FILTER_SANITIZE_STRING,
            'tourdate_venuecity' => FILTER_SANITIZE_STRING,
            'tourdate_venuepostcode' => FILTER_SANITIZE_STRING,
            'tourdate_venuecountry' => FILTER_SANITIZE_STRING,
            'tourdate_venuelink' => FILTER_SANITIZE_STRING,
            'tourdate_bookingnumber' => FILTER_SANITIZE_STRING,
            'tourdate_location' => FILTER_VALIDATE_INT,
            'tourdate_onsale' => FILTER_VALIDATE_INT,
            'tourdate_onmap' => FILTER_VALIDATE_INT
        ));

        foreach ($filtered_data as $key => $value) {
            update_post_meta($post_id, $key, $value);
        }
    }
    
    /**
    * Change admin menu
    */
    public function change_admin_menu() {
		add_submenu_page('edit.php?post_type=tourdate', 'Tour Dates Settings', 'Settings', 'edit_posts', 'tourdates-settings', array($this, 'add_option_page'));
	}
		
    /**
    * Add option page
    */
    public function add_option_page() {
    	
    	global $wpdb;
    	// Set up a few variables
    	$table_name = $wpdb->prefix."tourdatelocation";
    	$message = '';
    	$default_location = 0;
    	
    	// Initiate which tab we're on
    	$tab = 'themesettings';
    	if(isset($_GET['tab']) && !empty($_GET['tab'])) {
    		$tab = $_GET['tab'];
    	}
    	
    	// Delete the city
    	if(isset($_GET['delete_location']) && is_numeric($_GET['delete_location']) && $_GET['delete_location'] > 0) {
    		if($wpdb->get_row("SELECT * FROM $table_name WHERE id = ".$_GET['delete_location'])) {
    			$wpdb->delete($table_name, array('id' => $_GET['delete_location']));
    			$message = '<div id="message" class="fadeOut updated below-h2"><p>Location deleted.</p></div>';
    		}
    	}
    	
    	if(isset($_GET['reset_default']) && $_GET['reset_default'] == 1) {
    		$_POST['tourdate_map'] = AKATOURDATES_PLUGIN_URL.'library/images/map.png';
    	}
    	
    	if(isset($_GET['generate']) && $_GET['generate'] == 1) {
    		$template_parts_dir = get_template_directory().'/tour/';
    		if(!is_dir($template_parts_dir)) {
    			mkdir($template_parts_dir);
    		}
    		copy(AKATOURDATES_PLUGIN_DIR.'/library/assets/template_parts/dates-current.php', $template_parts_dir.'dates-current.php');
    		copy(AKATOURDATES_PLUGIN_DIR.'/library/assets/template_parts/dates-list.php', $template_parts_dir.'dates-list.php');
    		copy(AKATOURDATES_PLUGIN_DIR.'/library/assets/template_parts/dates-map.php', $template_parts_dir.'dates-map.php');
    		copy(AKATOURDATES_PLUGIN_DIR.'/library/assets/template_parts/dates-table.php', $template_parts_dir.'dates-table.php');
    		add_user_meta($user_id, 'tourdates_ignore_template_parts_notice', 'true', true);
    		$message = '<div id="message" class="fadeOut updated below-h2"><p>Template parts generated in your theme.</p></div>';
    	}
    	
    	// Update the city details or the image map
    	if(isset($_POST) && !empty($_POST) && (isset($_POST['tourdate_location_id']) || isset($_POST['tourdate_map']))) {
    		if(isset($_POST['tourdate_location_id']) && is_numeric($_POST['tourdate_location_id']) && $_POST['tourdate_location_id'] > 0) {
    			// Location update
    			if($wpdb->get_row("SELECT * FROM $table_name WHERE id = ".$_POST['tourdate_location_id'])) {
    				$default_location = $_POST['tourdate_location_id'];
    				$filtered_data = filter_var_array($_POST, array(
        			    'tourdate_location_currentname' => FILTER_SANITIZE_STRING,
        			    'tourdate_location_x' => FILTER_VALIDATE_INT,
        			    'tourdate_location_y' => FILTER_VALIDATE_INT
        			));    				
    				$coord = json_encode(array('x' => $filtered_data['tourdate_location_x'], 'y' => $filtered_data['tourdate_location_y']));
					if($wpdb->update($table_name, array( 'name' => $filtered_data['tourdate_location_currentname'], 'coordinates' => $coord ), array( 'id' => $_POST['tourdate_location_id'] ))) {
						$message = '<div id="message" class="fadeOut updated below-h2"><p>Location updated.</p></div>';
					}
    			}
    		} else {
    			// Map update
    			if(isset($_POST['tourdate_map']) && !empty($_POST['tourdate_map'])){
    				$tourdate_map = get_option('tourdate_map'); if(empty($tourdate_map)) { $tourdate_map = AKATOURDATES_PLUGIN_URL.'library/images/map.png'; }
    				$current_size = getimagesize($tourdate_map);
    				$current_ratio = round($current_size[0] / $current_size[1], 3, PHP_ROUND_HALF_UP);
    				$image_size = getimagesize($_POST['tourdate_map']);
					$image_ratio = round($image_size[0] / $image_size[1], 3, PHP_ROUND_HALF_UP);
    				if($image_ratio != $current_ratio) {
    					$message = '<div id="message" class="error below-h2"><p>Please upload an image in the correct ratio (1:1.25)</p></div>';
    				} else {
    					$locations = $wpdb->get_results("SELECT * FROM $table_name ORDER BY name ASC;");
           				update_option('tourdate_map', $_POST['tourdate_map']);
           				foreach($locations as $location) {
           				    $coord = json_decode($location->coordinates, true);
							$new_coord = array();
							$new_coord['x'] = round(($image_size[0] * $coord['x']) / $current_size[0]);
							$new_coord['y'] = round(($image_size[1] * $coord['y']) / $current_size[1]);
							$new_coord = json_encode($new_coord);
							$wpdb->update($table_name, array( 'coordinates' => $new_coord ), array( 'id' => $location->id ));
							$message = '<div id="message" class="fadeOut updated below-h2"><p>Map image updated.</p></div>';
           				}
    				}
    			}
    		}
    	}
    	    	
    	$map_url = AKATOURDATES_PLUGIN_URL.'library/images/map.png';
 		$tourdate_map = get_option('tourdate_map');
    	if(!empty($tourdate_map)) {
    		$map_url = $tourdate_map;
    	}

    	?>
    	<script type="text/javascript">
        	var page_url = '<?php echo admin_url('/edit.php?post_type=tourdate&page=tourdates-settings'); ?>';
        </script>
    	<div class="wrap">
    		<div id="icon-options-general" class="icon32"><br></div>
	    	<h2>Tour Dates Settings</h2>
	    	<h2 class="nav-tab-wrapper">
				<a href="edit.php?post_type=tourdate&page=tourdates-settings&tab=themesettings" class="nav-tab <?php if($tab == 'themesettings') { echo 'nav-tab-active'; } ?>">Theme Settings</a>
				<a href="edit.php?post_type=tourdate&page=tourdates-settings&tab=shortcodes-td" class="nav-tab <?php if($tab == 'shortcodes-td' || $tab == 'shortcodes-cl') { echo 'nav-tab-active'; } ?>">Shortcodes</a>
				<a href="edit.php?post_type=tourdate&page=tourdates-settings&tab=locations" class="nav-tab <?php if($tab == 'locations') { echo 'nav-tab-active'; } ?>">Locations</a>
				<a href="edit.php?post_type=tourdate&page=tourdates-settings&tab=map" class="nav-tab <?php if($tab == 'map') { echo 'nav-tab-active'; } ?>">Map</a>
			</h2>
			<?php
			switch($tab) {
				case 'themesettings':
					// Theme Settings Tour Dates Tab
					
					if($message) { echo $message; } 
					?>
					<p>
						You can display the tour dates information in your theme using template parts.<br />
						Template parts are independant files living in your theme, and they only display a specific functionality. They can be included in any file (header.php, footer.php, page templates...)
					</p>
					<?php
					$template_parts = false;
					$template_parts_dir = get_template_directory().'/tour/';
					if(is_dir($template_parts_dir)) {
						if(is_file($template_parts_dir.'dates-current.php') && is_file($template_parts_dir.'dates-list.php') && is_file($template_parts_dir.'dates-map.php') && is_file($template_parts_dir.'dates-table.php')) {
							$template_parts = true;
						}
					}
					if($template_parts) {
						?>
						<p>
							The tour dates template parts are already installed in your theme, you can use them by copying the below snippets in your files:
							<ul>
								<li><code><?php echo htmlentities("<?php get_template_part('tour/dates', 'list'); ?>"); ?></code> <em>Display all the tour dates in a HTML list markup</em></li>
								<li><code><?php echo htmlentities("<?php get_template_part('tour/dates', 'table'); ?>"); ?></code> <em>Display all the tour dates in a HTML table markup</em></li>
								<li><code><?php echo htmlentities("<?php get_template_part('tour/dates', 'map'); ?>"); ?></code> <em>Display all the tour dates on a map</em></li>
								<li><code><?php echo htmlentities("<?php get_template_part('tour/dates', 'current'); ?>"); ?></code> <em>Display all the tour dates in a HTML list markup</em></li>
							</ul> 
						</p>
						<p>
							You want to customise the markup and the data displayed? Have a look at the files in the tour folder of your theme.<br />
							If you don't feel comfortable changing these files, or if you encounter a problem, please speak to a back-end developer :)
						</p>
						<?php
					} else {
						?>
						<p><a class="button" href="edit.php?post_type=tourdate&page=tourdates-settings&tab=themesettings&generate=1" >Click to generate the template parts in your theme</a></p>
						<?php
					}
					
					break;
					
				case 'shortcodes-td': 
					// Shortcode Tour Dates Tab
					
					?>
					<h3 class="nav-tab-wrapper">
						<a href="edit.php?post_type=tourdate&page=tourdates-settings&tab=shortcodes-td" class="nav-tab <?php if($tab == 'shortcodes-td') { echo 'nav-tab-active'; } ?>">Tour Dates</a>
						<a href="edit.php?post_type=tourdate&page=tourdates-settings&tab=shortcodes-cl" class="nav-tab">Current Location</a>
					</h3>
					<p>This shortcode will display all of the tour dates, either in a list, in a table or on a map (UK only).<br>Edit the form to change the shortcode and then copy and paste it anywhere.<br /><strong>If you paste it in an editor, be careful to use the Visual view and not the Text one (in order to avoid HTML encoding problems).</strong><br />Click the box below to select it.</p>
					<p>
						<input type="text" id="tourdates_shortcode" value="" readOnly />
					</p>
					<table class="form-table settings shortcode_table">
	    			    <tr>
	    			    	<th scope="row"><label for="std_view">View</label></th>
	    			    	<td>
	    			    		<select id="std_view"><option value="list">List</option><option value="table">Table</option><option value="map">Map</option></select>
	    			    	</td>
	    				</tr>
	    			    <tr>
	    			    	<th scope="row"><label>Output Order</label></th>
	    			    	<td>
	    			    		<select id="std_outputorder1">
	    			    			<option value="d" selected>Dates</option>
	    			    			<option value="l">Location</option>
	    			    			<option value="b">Booking</option>
	    			    		</select>
	    			    		&nbsp;<strong>&middot;</strong>&nbsp;
	    			    		<select id="std_outputorder2">
	    			    			<option value="d" disabled>Dates</option>
	    			    			<option value="l" selected>Location</option>
	    			    			<option value="b">Booking</option>
	    			    		</select>
	    			    		&nbsp;<strong>&middot;</strong>&nbsp;
	    			    		<select id="std_outputorder3">
	    			    			<option value="d" disabled>Dates</option>
	    			    			<option value="l" disabled>Location</option>
	    			    			<option value="b" selected>Booking</option>
	    			    		</select>
	    			    	</td>
	    			    </tr>
	    			    <tr>
	    			    	<th scope="row"><label for="std_dateformat">Date Format (<a href="http://uk1.php.net/manual/en/function.date.php" target="_blank">more info</a>)</label></th>
	    			    	<td>
	    			    		<input type="text" id="std_dateformat">
	    			    		<br><em>Default: j M Y<br>Example: 7 Aug 2013</em>
	    			    	</td>
	    			    </tr>
	    			    <tr>
	    			    	<th scope="row"><label for="std_tag">Dates Tag<br><em>This tag wraps the dates information</em></label></th>
	    			    	<td>
	    			    		<select class="std_tag" id="std_datetag" data-stdlabel="dates_tag">
	    			    			<option value="div">div</option>
	    			    			<option value="h2">h2</option>
	    			    			<option value="h3">h3</option>
	    			    			<option value="h4">h4</option>
	    			    			<option value="h5">h5</option>
	    			    			<option value="h6">h6</option>
	    			    		</select>
	    			    	</td>
	    			    </tr>
	    			    <tr>
	    			    	<th scope="row"><label for="std_locationformat">Location Format<br><em>You can use custom tags to display the tour date informations.<br>See tips below</em></label></th>
	    			    	<td>
	    			    		<input type="text" id="std_locationformat">
	    			    		<br><em>Default: %open_venue_link%%name%%close_link% - %city% <br>Example: Theatre Royal Haymarket - London</em>
	    			    	</td>
	    			    </tr>
	    			    <tr>
	    			    	<th scope="row"><label for="std_locationtag">Location Tag<br><em>This tag wraps the location information</em></label></th>
	    			    	<td>
	    			    		<select class="std_tag" id="std_locationtag" data-stdlabel="location_tag">
	    			    			<option value="div">div</option>
	    			    			<option value="h2">h2</option>
	    			    			<option value="h3">h3</option>
	    			    			<option value="h4">h4</option>
	    			    			<option value="h5">h5</option>
	    			    			<option value="h6">h6</option>
	    			    		</select>
	    			    	</td>
	    			    </tr>
	    			    <tr>
	    			    	<th scope="row"><label for="std_bookingformat">Booking Format<br><em>You can use custom tags to display the tour date informations.<br>See tips below</em>	</label></th>
	    			    	<td>
	    			    		<input type="text" id="std_bookingformat">
	    			    		<br><em>Default: %open_booking_link%Book online%close_link% or call %booking_number%<br> Example: <a class="btn" href="http://www.akauk.com/" target="_blank">Book online</a> or call 08 00 000 000</em>
	    			    	</td>
	    			    </tr>
	    			    <tr>
	    			    	<th scope="row"><label for="std_bookingtag">Booking Tag<br><em>This tag wraps the booking information</em></label></th>
	    			    	<td>
	    			    		<select class="std_tag" id="std_bookingtag" data-stdlabel="booking_tag">
	    			    			<option value="div">div</option>
	    			    			<option value="h2">h2</option>
	    			    			<option value="h3">h3</option>
	    			    			<option value="h4">h4</option>
	    			    			<option value="h5">h5</option>
	    			    			<option value="h6">h6</option>
	    			    		</select>
	    			    	</td>
	    			    </tr>
                <tr>
                  <th scope="row">Show Past Dates</th>
                  <td><select name="std_pastdates" id="std_pastdates"><option value="1">Yes</option><option value="0">No</option></select></td>
                </tr>
	    			    <tr>
	    			    	<th scope="row"><label for="std_uniqueid">Unique ID</label></th>
	    			    	<td><input type="text" id="std_uniqueid"></td>
	    			    </tr>
					</table>
					<p>&nbsp;</p>
					<hr>
					<h3>Tips</h3>
					<p>You can use the below custom tags in 'Location Format' and 'Booking Format'</p>
					<p>
						<ul>
							<li><strong>%title%</strong> <em>will display the main title of the tour date</em></li>
							<li><strong>%name%</strong> <em>will display the name of the venue</em></li>
							<li><strong>%city%</strong> <em>will display the city filled up in the venue details</em></li>
							<li><strong>%address1%</strong> <em>will display the first line of the address filled up in the venue details</em></li>
							<li><strong>%address2%</strong> <em>will display the first line of the address filled up in the venue details</em></li>
							<li><strong>%post_code%</strong> <em>will display the post code filled up in the venue details</em></li>
							<li><strong>%country%</strong> <em>will display the country selected in the venue details</em></li>
              <li><strong>%open_venue_link%</strong> <em>will display an opening anchor tag for the venue link</em></li>
							<li><strong>%open_booking_link%</strong> <em>will display an opening anchor tag for the booking link</em></li>
              <li><strong>%open_permalink%</strong> <em>will display an opening anchor tag for the permalink</em></li>
							<li><strong>%close_link%</strong> <em>will display a closing anchor tag</em></li> 
              <li><strong>%venue_link%</strong> <em>will display the venue link</em></li>
              <li><strong>%booking_link%</strong> <em>will display the booking link</em></li>
              <li><strong>%permalink%</strong> <em>will display the permalink</em></li>
							<li><strong>%booking_number%</strong> <em>will display the booking number</em></li>
						</ul>
					</p>
					
					<?php
				
					break;
				
				case 'shortcodes-cl': 
					// Shortcode Current Location Tab
					
					?>
					<h3 class="nav-tab-wrapper">
						<a href="edit.php?post_type=tourdate&page=tourdates-settings&tab=shortcodes-td" class="nav-tab">Tour Dates</a>
						<a href="edit.php?post_type=tourdate&page=tourdates-settings&tab=shortcodes-cl" class="nav-tab <?php if($tab == 'shortcodes-cl') { echo 'nav-tab-active'; } ?>">Current Location</a>
					</h3>

					<p>This shortcode will display the current tour date.<br>Edit the form to change the shortcode and then copy and paste it anywhere.<br /><strong>If you paste it in an editor, be careful to use the Visual view and not the Text one (in order avoid HTML encoding problems).</strong><br />Click the box below to select it.</p>
					<p>
						<input type="text" id="currentlocation_shortcode" value="" readOnly />
					</p>
					<table class="form-table settings shortcode_table">
	    				<tr>
	    				    <th scope="row"><label for="scl_dateformat">Date Format (<a href="http://uk1.php.net/manual/en/function.date.php" target="_blank">more info</a>)</label></th>
	    				    <td>
	    				    	<input type="text" id="scl_dateformat">
	    				    	<br><em>Default: j M Y<br>Example: 7 Aug 2013</em>
	    				    </td>
	    				</tr>
	    				<tr>
	    				    <th scope="row"><label for="scl_output">Output<br><em>You can use custom tags to display the tour date informations.<br>See tips below</em></label></th>
	    				    <td>
	    				    	<textarea id="scl_output" class="big_textarea"></textarea>
	    				    	<br><em>Default: <?php echo htmlentities("<h2>Currently playing at %name%</h2><p>%open_booking_link%Book online%close_link% or call %booking_number%</p>"); ?><br>Example: <strong>Currently playing at London Theatre</strong> <a href="#">Book online</a> or call 08 00 000 000</em>
	    				    </td>
	    				</tr>
	    				<tr>
	    				    <th scope="row"><label for="scl_nowhere_output">Currently Nowhere Output <br><em>Output used when the tour is in between locations.<br>You can use custom tags to display the tour date informations.<br>See tips below</em></label></th>
	    				    <td>
	    				    	<textarea id="scl_nowhere_output" class="big_textarea"></textarea>
	    				    	<br><em>Default: <?php echo htmlentities("<h2>Next performance at %name% from %start_date%</h2><p>%open_booking_link%Book online%close_link% or call %booking_number%</p>"); ?><br>Example: <strong>Next performance at London Theatre from 7 Aug 2013</strong> <a href="#">Book online</a> or call 08 00 000 000</em>
	    				    </td>
	    				</tr>
	    				<tr>
	    				    <th scope="row"><label for="scl_tourend_output">Tour End Output <br><em>Output used when the tour is over</em></label></th>
	    				    <td>
	    				    	<textarea id="scl_tourend_output" class="big_textarea"></textarea>
	    				    	<br><em>Default: <?php echo htmlentities("<h2>The tour is now finished!</h2>"); ?><br>Example: <strong>The tour is now finished!</strong></em>
	    				    </td>
	    				</tr>
					</table>
					<p>&nbsp;</p>
					<hr>
					<h3>Tips</h3>
					<p>You can use the below custom tags in 'Output', 'Output No Current' and 'Output Tour End'</p>
					<p>
						<ul>
							<li><strong>%start_date%</strong> <em>will display the start date</em></li>
							<li><strong>%end_date%</strong> <em>will display the end date</em></li>
							<li><strong>%title%</strong> <em>will display the main title of the tour date</em></li>
							<li><strong>%name%</strong> <em>will display the name of the venue</em></li>
							<li><strong>%city%</strong> <em>will display the city filled up in the venue details</em></li>
							<li><strong>%address1%</strong> <em>will display the first line of the address filled up in the venue details</em></li>
							<li><strong>%address2%</strong> <em>will display the first line of the address filled up in the venue details</em></li>
							<li><strong>%post_code%</strong> <em>will display the post code filled up in the venue details</em></li>
							<li><strong>%country%</strong> <em>will display the country selected in the venue details</em></li>
							<li><strong>%open_venue_link%</strong> <em>will display an opening anchor tag for the venue link</em></li>
							<li><strong>%open_booking_link%</strong> <em>will display an opening anchor tag for the booking link</em></li>
              <li><strong>%open_permalink%</strong> <em>will display an opening anchor tag for the permalink</em></li>
							<li><strong>%close_link%</strong> <em>will display a closing anchor tag</em></li>
              <li><strong>%venue_link%</strong> <em>will display the venue link</em></li>
              <li><strong>%booking_link%</strong> <em>will display the booking link</em></li>
              <li><strong>%permalink%</strong> <em>will display the permalink</em></li>
							<li><strong>%booking_number%</strong> <em>will display the booking number</em></li>
						</ul>
					</p>
					
					<?php
				
					break;

				
				case 'locations':
					// Locations Tab
					
					if($message) { echo $message; } ?>
	    			<form action="edit.php?post_type=tourdate&page=tourdates-settings&tab=locations" method="POST">
	    				<table class="form-table settings">
	    					<tr>
	    						<th scope="row"><label>Location to edit</label></th>
	    						<td>
	    							<select name="tourdate_location" id="tourdate_location">
           					   		    <?php
           					   		    $locations = $wpdb->get_results("SELECT * FROM $table_name ORDER BY name ASC;");
           					   		    foreach($locations as $location) {
           					   		    	$coord = json_decode($location->coordinates);
           					   		    	?>
           					   		    	<option data-x="<?php echo $coord->x; ?>" data-y="<?php echo $coord->y; ?>" <?php if($default_location == $location->id) { echo "selected"; } ?> value="<?php echo $location->id; ?>"><?php echo $location->name; ?></option>
           					   		    	<?php
           					   		    }
           					   		    ?>
           					   		</select>
        							<input type="hidden" name="tourdate_location_id" id="tourdate_location_id">
						    		<input type="hidden" name="tourdate_location_x" id="tourdate_location_x">
						    		<input type="hidden" name="tourdate_location_y" id="tourdate_location_y">
	    						</td>
	    					</tr>
	    					<tr>
	    					    <th scope="row"><label>Name</label></th>
	    					    <td><input type="text" name="tourdate_location_currentname" id="tourdate_location_currentname" value=""></td>
	    					</tr>
	    					<tr>
           					    <th scope="row"><label>Marker</label></th>
           					    <td>
           					    	<em>Click anywhere on the below image to change the position of the marker</em>
           					    	<br />
           					    	<div id="tourdatemap" class="clickable">
           					    		<?php $tourdate_map = get_option('tourdate_map'); if(empty($tourdate_map)) { $tourdate_map = AKATOURDATES_PLUGIN_URL.'/library/images/map.png'; } ?>
           					    		<img class="map" src="<?php echo $tourdate_map; ?>">
           					    		<img class="marker" src="<?php echo AKATOURDATES_PLUGIN_URL; ?>library/images/marker.png" >
           					    	</div>
           					    	
           					    </td>
           					</tr>
	    				</table>
	    				<p id="tourdate_edit_location_submit" class="submit">
	    					<input type="submit" name="submit" id="submit" class="button button-primary" value="Save This Location">
	    					<input type="submit" id="delete" class="button button-secondary" value="Delete This Location">
	    				</p>
    				</form>
    				<?php
					
					break;
					
				case 'map':
					// Map Tab
					
					if($message) { echo $message; }
					?>
					<form action="edit.php?post_type=tourdate&page=tourdates-settings&tab=map" id="form_map" method="POST">
						<p>
							Below is the map being used at the moment. Be careful if you change it as the ratio needs to be the same (1:1.25).
							<br>
							<a href="<?php echo AKATOURDATES_PLUGIN_URL.'library/assets/tourmap.psd'; ?>">Download the default image PSD file</a>. You can upload a bigger or a smaller image, as long as the ratio stays the same.
						</p>
						<p><br><img class="current_map" src="<?php echo $map_url; ?>"></p>
						<div class="uploader">
						  <input type="hidden" name="tourdate_map" id="tourdate_map" />
						  <input type="button" class="button" name="tourdate_map_button" id="tourdate_map_button" value="Select another image" />&nbsp;&nbsp;<input type="button" class="button" name="tourdate_map_button_reset" id="tourdate_map_button_reset" value="Reset to default image" />
						</div>
					</form>
					<?php
					
					break;
			}
			?>
    	</div>
    	<?php
    	
    }
    
    public function display_admin_notice() {
    	
    	$template_parts = false;
		$template_parts_dir = get_template_directory().'/tour/';
		if(is_dir($template_parts_dir)) {
		    if(is_file($template_parts_dir.'dates-current.php') && is_file($template_parts_dir.'dates-list.php') && is_file($template_parts_dir.'dates-map.php') && is_file($template_parts_dir.'dates-table.php')) {
		    	$template_parts = true;
		    }
		}			
		
		if(!$template_parts) {
    	
    		global $current_user ;
        	$user_id = $current_user->ID;
	    	
	    	if (!get_user_meta($user_id, 'tourdates_ignore_template_parts_notice') && $_GET['page'] !== 'tourdates-settings') {
    			?>
    			<div class="updated">
	    		    <p>To generate the tour dates template parts in your theme, go on the <a href="edit.php?post_type=tourdate&page=tourdates-settings&tab=themesettings">Tour Dates Settings</a>&nbsp;&nbsp;&nbsp;<?php printf(__('<a class="button" href="%1$s">Hide</a>'), '?tourdates_ignore_template_parts_notice=0'); ?>
    			</div>
	    		<?php
	    	}
	    	
	    }
	    
    }


}