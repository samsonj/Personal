// AKA Base Javascript
// Version 0.5
//
// Authors: Jeremy Basolo, Ash Whiting, Matt Cegielka
   
// Concatinate the scripts into this file by using Grunt  
// see gruntfile.js for useage

// Suppress semicolon warnings                 
         
/* jshint asi: true */

(function(jQuery) {                        
          
    jQuery(document).ready(function(){

        resizeCart();   
        window.onresize = function(event) {
            resizeCart();  
        }
        function resizeCart() {  
            var cartHeight = jQuery(window).height();
            if(jQuery(window).width()<=767) {
                    jQuery('.top-cart .inner-wrapper').css({'height': cartHeight - 275 + 'px'});
                } else {
                    jQuery('.top-cart .inner-wrapper').css({'height': cartHeight - 375 + 'px'});        
            };  
        };
        jQuery(".inner-wrapper").mCustomScrollbar(
            {mouseWheel:true,contentTouchScroll:true,theme:"dark-thin",scrollButtons:{enable:false}}
        );
    });

})(jQuery);  

jQuery(function() {
    var dpr = 1;
    if(window.devicePixelRatio !== undefined) dpr = window.devicePixelRatio;
    jQuery(".retina").each( function() {
        var imgStr = jQuery(this).attr('image-x' + dpr);
        if(!imgStr )
            imgStr  = jQuery(this).attr('image-x1'); // fallback to 1x
        jQuery(this).attr('src',  imgStr);     
    });
});


// (function(jQuery){
//     jQuery(document).ready(function(){
//         resizeCart();

//         window.onresize = function(event) {
//             resizeCart();
//         }
//         function resizeCart() {
//             var cartHeight = jQuery(window).height();
//             if(jQuery(window).width()<=767) {
//                     jQuery('.top-cart .inner-wrapper').css({'height': cartHeight - 275 + 'px'});
//                 } else {
//                     jQuery('.top-cart .inner-wrapper').css({'height': cartHeight - 375 + 'px'});        
//             };
//         };
//         jQuery(".inner-wrapper").mCustomScrollbar(
//             {mouseWheel:true,contentTouchScroll:true,theme:"dark-thin",scrollButtons:{enable:false}}
//         );
//     });
// })(jQuery);

// jQuery(function() {
//     var dpr = 1;
//     if(window.devicePixelRatio !== undefined) dpr = window.devicePixelRatio;
//     jQuery(".retina").each( function() {
//         var imgStr = jQuery(this).attr('image-x' + dpr);
//         if(!imgStr )
//             imgStr  = jQuery(this).attr('image-x1'); // fallback to 1x
//         jQuery(this).attr('src',  imgStr);
//     });
// });

jQuery(document).ready(function($) {

    //CUSTOM JS
    jQuery(".header-sidebar").css("height", "auto");

    //SUBSCRIBE
    jQuery('.subscribe-now').click(function(){
        jQuery(".block-subscribe").slideToggle();
        return false;
    });        

    // Gallery lightgallery-all

    $("#image-gallery").lightGallery();

    // Video lightgallery-all

    $("#video-gallery").lightGallery({  
        thumbnail: false,  
        videoMaxWidth: '90%',  
        subHtml: '',
        fullScreen: false,
        youtubePlayerParams: {
            rel: 0   
        }  
    });

    // Share modal links
    $('.share-modal').click(function(ev) {

    	if($(window).width() > 600) {
		    ev.preventDefault();
	        window.open($(this).attr('href'), '', 'width=600,height=350');
	    } else {
	    	window.open($(this).attr('href'));
	    }

	});



});






