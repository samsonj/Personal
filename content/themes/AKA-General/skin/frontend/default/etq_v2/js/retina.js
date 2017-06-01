jQuery(function() {
    var dpr = 1;
    if(window.devicePixelRatio !== undefined) dpr = window.devicePixelRatio;
    //alert(dpr);
	jQuery(".retina").each( function() {
        var imgStr = jQuery(this).attr('image-x' + dpr);
        if(!imgStr )
            imgStr  = jQuery(this).attr('image-x1'); // fallback to 1x
        jQuery(this).attr('src',  imgStr);
    });
});