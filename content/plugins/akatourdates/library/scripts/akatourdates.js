var $ = jQuery;

$(window).load(function() {
	
	// For each li on the map, set the marker position
	$('.tourDates.map ul li div.marker').each(function() {
		var parentElem = $(this).parent();
		var varX = parentElem.data('x');
		var varY = parentElem.data('y');
		setMarker($(this), varX, varY);
		setMarker($('.details', parentElem), varX, varY);
		$(this).fadeIn();
	});
	
	// On Mouse over on every map li, display the details
	$('.tourDates.map .marker').on('click', '', function() {
		closeDetails();
		$(this).siblings('.details').fadeIn();
	});
	
	// On Mouse over on every map li, hide the details
	$('.tourDates.map .close').on('click', function() {
		closeDetails();
	});
	function closeDetails() {
		$('.tourDates.map .details').fadeOut();
	}
	
	// Function to set the position of a marker
	function setMarker(markerObject, x, y) {
		var leftPos = (x - (markerObject.width()/2));
		var topPos = (y - markerObject.height());
		markerObject.css({left: leftPos, top: topPos});
	}
		
});

