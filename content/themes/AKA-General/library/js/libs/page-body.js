function sizeContent() {
	// if (jQuery(window).height() < jQuery(document).height()){
	// 		jQuery('#wrap').css({'min-height': ''});
	// }else{
	// 		jQuery('#wrap').css({'height': jQuery(window).height() + 'px'});
	// 		jQuery('#wrap').css({'min-height': ''});
	// }
}
//page transitions
if (navigator.appName == "Microsoft Internet Explorer") {
	//disable page transitions IE
} else if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) {
	//disable page transitions Safari
} else {
	//page transitions nonIE
		jQuery(document).ready(function(){
			 jQuery('a.transition').click(function(event) {
			 newLocation = this.href;
				jQuery('#preloader').fadeIn(400, newpage);
				return false;
			 });
			 function newpage() {
				window.location = newLocation;
				
			 }
		});
}
jQuery(document).ready(function(){	
	jQuery('html, body').animate({scrollTop:0}, 'slow');
    jQuery("select").each(function(){
        jQuery(this).selectbox();
    });
});
jQuery(document).ready(sizeContent);
jQuery(window).resize(sizeContent);