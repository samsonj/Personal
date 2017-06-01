(function($) {
$(document).ready(function() {
	
	/*
	************************************************************
	* TOUR DATE POST TYPE
	************************************************************
	*/
	// Date picker for date fields
	$('.datePicker').each(function() {
		$(this).datepicker({ showAnim: 'blind', dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true });
	});
	$('#tourdatemap').width($('#tourdatemap .map').width());
	
	// Display booking info if necessary
	if($('#tourdate_onsale').is(':checked')) { $('#tourdate_booking_details').show(); $('#tourdate_notonsale_tr').hide(); }
	$('#tourdate_onsale, #tourdate_notonsale').change(function() { 
		if($('#tourdate_onsale').is(':checked')) { 
			$('#tourdate_notonsale_tr').fadeToggle(200, function () {
				$('#tourdate_booking_details').fadeToggle(200); 
			}); 
		} else {
			$('#tourdate_booking_details').fadeToggle(200, function () {
				$('#tourdate_notonsale_tr').fadeToggle(200); 
			}); 
		}
	});
	
	// Display the map only if the venue is located in the United Kingdom
	$('#tourdate_map').hide();
	if($('#tourdate_venuecountry').val() == 'United Kingdom' || $('#tourdate_venuecountry').val() == 'Ireland') {
		$('#tourdate_map').show();
	}
	$('#tourdate_venuecountry').change(function() {
		if($(this).val() == 'United Kingdom' || $(this).val() == 'Ireland') {
			$('#tourdate_map').fadeIn(200);
			if($('#tourdate_onmap').is(':checked')) { $('#tourdate_map_details').fadeIn(200); }
		} else {
			$('#tourdate_map').fadeOut(200);
			$('#tourdate_map_details').fadeOut(200);
		}
	});
	
	// Display map details if necessary
	if($('#tourdate_onmap').is(':checked')) { $('#tourdate_map_details').show(); }
	$('#tourdate_onmap').change(function() { $('#tourdate_map_details').fadeToggle(200); });
	
	// Display marker on the map
	if($('.posttype #tourdate_location').length > 0 && $('.posttype #tourdate_location').val() !== '') {
		setMarker($('#tourdate_location option:selected').data('x'), $('#tourdate_location option:selected').data('y'), false);
		$('#tourdatemap .marker').show();
	}
	
	// When the location is selected through the current list
	$('#tourdate_location').change(function() {
		if($(this).val() == '') {
			$('#tourdatemap .marker').fadeOut(200);
		} else {
			if($('#tourdatemap .marker').css('display') == 'none') { 
				setMarker($('#tourdate_location option:selected').data('x'), $('#tourdate_location option:selected').data('y'), false);
				$('#tourdatemap .marker').fadeIn(200); 
			} else {
				setMarker($('#tourdate_location option:selected').data('x'), $('#tourdate_location option:selected').data('y'), true);
			}
		}
	});
	
	// Handle the default value of the tour date location name text field
	$('#tourdate_location_name').val($('#tourdate_location_name').data('defaultvalue'));
	$('#tourdate_location_name').focus(function() { 
		if($('#tourdate_location_name').val() == $('#tourdate_location_name').data('defaultvalue')) { $('#tourdate_location_name').val(''); }
	}); 
	$('#tourdate_location_name').blur(function() { 
		if($('#tourdate_location_name').val() == '') { $('#tourdate_location_name').val($('#tourdate_location_name').data('defaultvalue')); }
	});
	
	// Adding a new location
	$('body').on('click', '#tourdate_location_td .add_location', function(ev) {
		ev.preventDefault();
		$('#tourdate_location_select').fadeOut(200, function() {
			$('#tourdate_location_add').fadeIn(200);
		});
		$('#tourdatemap .marker').fadeOut(200);
		$('#tourdatemap').addClass('clickable');
	});
	
	$('body').on('click', '#tourdatemap.clickable', function(ev) {
		ev.preventDefault();
		var offset = $(this).offset();
        var x = ev.pageX - offset.left;
        var y = ev.pageY - offset.top;
        if($('#tourdatemap .marker').css('display') !== 'none') {
			$('#tourdatemap .marker').fadeOut(200, function() { 
				setMarker(x, y, false);
				$('#tourdatemap .marker').fadeIn(200);
			});	
		} else {
			setMarker(x, y, false);
			$('#tourdatemap .marker').fadeIn(200);
		}
		$('#tourdate_location_x').val(x);
		$('#tourdate_location_y').val(y);
	});
	
	// Cancel adding a new location
	$('body').on('click', '#tourdate_location_td .select_location', function(ev) {
		ev.preventDefault();
		$('#tourdate_location_add').fadeOut(200, function() {
			$('#tourdate_location_name').val($('#tourdate_location_name').data('defaultvalue'));
			$('#tourdate_location_x').val('');
			$('#tourdate_location_y').val('');
			$('#tourdate_location_select').fadeIn(200);
		});
		if($('#tourdate_location').val() == '') {
			setMarker(0, 0, false);
			$('#tourdatemap .marker').hide();
		} else {
			if($('#tourdatemap .marker').css('display') == 'none') {
				setMarker($('#tourdate_location option:selected').data('x'), $('#tourdate_location option:selected').data('y'), false);
			} else {
				setMarker($('#tourdate_location option:selected').data('x'), $('#tourdate_location option:selected').data('y'), true);
			}
		}
		if($('#tourdate_location').val()) { $('#tourdatemap .marker').fadeIn(200); }
		$('#tourdatemap').removeClass('clickable');
	});	
	
	// Function to set the position of the marker on the page
	function setMarker(x, y, animate) {
		var leftPos = (x - ($('#tourdatemap .marker').width()/2));
		var topPos = (y - $('#tourdatemap .marker').height());
		if(animate) {
			$('#tourdatemap .marker').animate({left: leftPos, top: topPos}, 350);
		} else {
			$('#tourdatemap .marker').css({left: leftPos, top: topPos});
		}
	}
	
	
	
	/*
	************************************************************
	* SETTINGS LOCATIONS PAGE
	************************************************************
	*/
	$('.settings #tourdate_location_currentname').val($('.settings_page_tourdates-settings #tourdate_location option:selected').text());
	$('.settings #tourdate_location_id').val($('.settings_page_tourdates-settings #tourdate_location option:selected').val());
	$('.settings #tourdate_location_x').val($('#tourdate_location option:selected').data('x'));
	$('.settings #tourdate_location_y').val($('#tourdate_location option:selected').data('y'));
	// Display marker on the map
	if($('.settings #tourdate_location').length > 0 && $('.settings #tourdate_location').val() !== '') {
		setTimeout(function() {
			setMarker($('#tourdate_location option:selected').data('x'), $('#tourdate_location option:selected').data('y'), false);
			$('#tourdatemap .marker').show();	
		}, 300);
	}
	$('.settings #tourdate_location').change(function() {
		$('.settings #tourdate_location_currentname').val($('.settings #tourdate_location option:selected').text());
		$('.settings #tourdate_location_id').val($('.settings #tourdate_location option:selected').val());
		$('.settings #tourdate_location_x').val($('.settings #tourdate_location option:selected').data('x'));
		$('.settings #tourdate_location_y').val($('.settings #tourdate_location option:selected').data('y'));
	});
	$('#tourdate_edit_location_submit #delete').click(function(ev) {
		ev.preventDefault();
		if(confirm('Do you really want to delete ' + $('#tourdate_location_currentname').val() + '?\n\nIf there is any tour date associated with this location, it won\'t appear on the map anymore.\n\nThink about it.')) {
			window.location.href = page_url + '&tab=locations&delete_location=' + $('#tourdate_location_id').val();
		}
	});
	$('body').on('click', '.settings #tourdatemap.clickable', function(ev) {
		var offset = $(this).offset();
        var x = ev.pageX - offset.left;
        var y = ev.pageY - offset.top;
		$('.settings #tourdate_location_x').val(x);
		$('.settings #tourdate_location_y').val(y);
	});
	setTimeout(function(){ $('.fadeOut').fadeOut(500); }, 2000);
	
	/*
	************************************************************
	* SETTINGS MAP PAGE
	************************************************************
	*/
	// Handle image uploads
	var file_frame;
	
	jQuery('#tourdate_map_button').on('click', function(event) {
 
        event.preventDefault();
        
        //Set target fields
		targetfield = jQuery(this).siblings('input');
		previewimage = jQuery(this).parent().prev().children('img');
 
        //If the uploader object has already been created, reopen the dialog
        if (file_frame) {
            file_frame.open();
            return;
        }
 
        //Extend the wp.media object
        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
 
        //When a file is selected, grab the URL and set it as the text field's value
        file_frame.on('select', function() {
		
            attachment = file_frame.state().get('selection').first().toJSON();
            $('#tourdate_map').val(attachment.url);
            $('#form_map').submit();
            
        });
 
        //Open the uploader dialog
        file_frame.open();
 
    }); 
    
    jQuery('#tourdate_map_button_reset').on('click', function(event) {
 
        event.preventDefault();
        window.location.href = page_url + '&tab=map&reset_default=1';
        
    }); 
    
    /*
	************************************************************
	* SETTINGS SHORTCODE PAGE
	************************************************************
	*/
	// Click event on the shortcodes
	$('#tourdates_shortcode').click(function() { $(this).select(); });
	$('#currentlocation_shortcode').click(function() { $(this).select(); });
	
	// Generating the tour dates shortcode
	var generateTourDatesShortcode = function() {
	    var shortcode = "[tourdates view='" + $('#std_view').val() + "'";
	    shortcode = shortcode + " output_order='" + $('#std_outputorder1').val() + $('#std_outputorder2').val() + $('#std_outputorder3').val() + "'";
	    $('.std_tag').each(function() { shortcode = shortcode + " " + $(this).data('stdlabel') + "='" + $(this).val() + "'"; });
	    if($('#std_dateformat').val()) { shortcode = shortcode + " date_format='" + $('#std_dateformat').val().replace(/\'/g,"\\'").replace(/\"/g,'\\"')  + "'"; }
	    if($('#std_locationformat').val()) { shortcode = shortcode + " location_format='" + $('#std_locationformat').val().replace(/\'/g,"\\'").replace(/\"/g,'\\"') + "'"; }
	    if($('#std_bookingformat').val()) { shortcode = shortcode + " booking_format='" + $('#std_bookingformat').val().replace(/\'/g,"\\'").replace(/\"/g,'\\"') + "'"; }
	    if($('#std_pastdates').val()) { shortcode = shortcode + " show_past_dates=" + $('#std_pastdates').val() + ""; }
	    if($('#std_uniqueid').val()) { shortcode = shortcode + " unique_id='" + $('#std_uniqueid').val() + "'"; }
	    shortcode = shortcode + "]";
	    $('#tourdates_shortcode').val(shortcode);
	};
	// Output order select boxes update	
	$('#std_outputorder1').change(function() {
		$('#std_outputorder2 option, #std_outputorder3 option').attr('disabled', false); $('#std_outputorder2 option, #std_outputorder3 option').attr('selected', false);
		$('#std_outputorder2 option[value="' + $(this).val() + '"]').attr('disabled', true); $("#std_outputorder2 option:not([disabled])").first().attr("selected", "selected");
		$('#std_outputorder3 option[value="' + $(this).val() + '"]').attr('disabled', true); $('#std_outputorder3 option[value="' + $('#std_outputorder2').val() + '"]').attr('disabled', true);
		$("#std_outputorder3 option:not([disabled])").first().attr("selected", "selected");
		generateTourDatesShortcode();
	});
	$('#std_outputorder2').change(function() {
		$('#std_outputorder3 option').attr('disabled', false); $('#std_outputorder3 option').attr('selected', false);
		$('#std_outputorder3 option[value="' + $(this).val() + '"]').attr('disabled', true); $('#std_outputorder3 option[value="' + $('#std_outputorder1').val() + '"]').attr('disabled', true);
		$("#std_outputorder3 option:not([disabled])").first().attr("selected", "selected");
		generateTourDatesShortcode();
	});
	generateTourDatesShortcode();
	$('#std_view').change(function() { generateTourDatesShortcode(); });
	$('#std_dateformat').keyup(function() { generateTourDatesShortcode(); });
	$('#std_locationformat').keyup(function() { generateTourDatesShortcode(); });
	$('#std_bookingformat').keyup(function() { generateTourDatesShortcode(); });
	$('#std_pastdates').change(function() { generateTourDatesShortcode(); });
	$('#std_uniqueid').keyup(function() { generateTourDatesShortcode(); });
	$('.std_tag').change(function() { generateTourDatesShortcode(); });
	
	// Generating the current location shortcode
	var generateCurrentLocationShortcode = function() {
	    var shortcode = "[currentlocation";
	    if($('#scl_dateformat').val()) { shortcode = shortcode + " date_format='" + $('#scl_dateformat').val() + "'"; }
	    if($('#scl_output').val()) { shortcode = shortcode + " output='" + $('#scl_output').val().replace(/(\r\n|\n|\r)/gm,"").replace(/\'/g,"\\'").replace(/\"/g,'\\"') + "'"; }
	    if($('#scl_nowhere_output').val()) { shortcode = shortcode + " nowhere_output='" + $('#scl_nowhere_output').val().replace(/(\r\n|\n|\r)/gm,"").replace(/\'/g,"\\'").replace(/\"/g,'\\"') + "'"; }
	    if($('#scl_tourend_output').val()) { shortcode = shortcode + " tourend_output='" + $('#scl_tourend_output').val().replace(/(\r\n|\n|\r)/gm,"").replace(/\'/g,"\\'").replace(/\"/g,'\\"') + "'"; }
	    shortcode = shortcode + "]";
	    $('#currentlocation_shortcode').val(shortcode);
	};
	generateCurrentLocationShortcode();
	$('#scl_dateformat').keyup(function() { generateCurrentLocationShortcode(); });
	$('#scl_output').keyup(function() { generateCurrentLocationShortcode(); });
	$('#scl_nowhere_output').keyup(function() { generateCurrentLocationShortcode(); });
	$('#scl_tourend_output').keyup(function() { generateCurrentLocationShortcode(); });
	
	
});
})(jQuery);
