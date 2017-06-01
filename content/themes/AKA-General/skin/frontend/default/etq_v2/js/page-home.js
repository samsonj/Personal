jQuery(function($){
	$("#mob-overlay-bg").bgStretcher({
		images: ["./media/mob-slide1.jpg", "./media/mob-slide2.jpg", "./media/mob-slide3.jpg"],
		imageWidth: 919,imageHeight: 668,nextSlideDelay: 5000, sequenceMode: "normal",
		resizeProportionally: true,transitionEffect: "fade"});
});


if(jQuery(window).width()>=1500 && jQuery(window).width()<=1999) {
	//load 1500+ slider
	jQuery(function($){
		$("#overlay-top").bgStretcher({
			images: ["./media/slide1_1500.jpg", "./media/slide2_1500.jpg", "./media/slide3_1500.jpg"],
			imageWidth: 1500,imageHeight: 668,nextSlideDelay: 5000,
			resizeProportionally: true,transitionEffect: "fade", sequenceMode: "normal", buttonNext: ".next",buttonPrev: ".prev",pagination: ".overlay-pagination"});
	});p[o;'QA']

}else if(jQuery(window).width()>=2000){
	//load retina slider
	jQuery(function($){
		$("#overlay-top").bgStretcher({
			images: ["./media/slide1_retina.jpg", "./media/slide2_retina.jpg", "./media/slide3_retina.jpg"],
			imageWidth: 1500,imageHeight: 668,nextSlideDelay: 5000,
			resizeProportionally: true,transitionEffect: "fade", sequenceMode: "normal", buttonNext: ".next",buttonPrev: ".prev",pagination: ".overlay-pagination"});
	});

}else {
	//load normal slider
	jQuery(function($){
		$("#overlay-top").bgStretcher({
			images: ["./media/slide1.jpg", "./media/slide2.jpg", "./media/slide3.jpg"],
			imageWidth: 1500,imageHeight: 668,nextSlideDelay: 5000,
			resizeProportionally: true,transitionEffect: "fade", sequenceMode: "normal", buttonNext: ".next",buttonPrev: ".prev",pagination: ".overlay-pagination"});
	});
};


jQuery(window).scroll(function() {
	var isiPad = navigator.userAgent.toLowerCase().indexOf("ipad");
	if(isiPad > -1)
	{
		if (jQuery(this).scrollTop() < 3500) {
			jQuery(".footer-container").hide();
			jQuery(".bgstretcher-area").show();
		}
		else {
			jQuery(".footer-container").show();
			jQuery(".bgstretcher-area").hide();
		}				
	}else{
		if (jQuery(this).scrollTop() < 1000) {
			jQuery(".footer-container").hide();
			jQuery(".bgstretcher-area").show();
		}
		else {
			jQuery(".footer-container").show();
			jQuery(".bgstretcher-area").hide();
		}
	};

});

	var canSeeMenu = true;
	jQuery(window).scroll(function() {
		if (jQuery(this).scrollTop() > jQuery(window).height() - 190 && canSeeMenu) {
			jQuery(".box-scroll").css("top", "222px");
			jQuery(".header-sidebar").css("height", "auto");
			jQuery(".box-scroll").css("height", "300px");
			canSeeMenu = false;
		} else if (jQuery(this).scrollTop() <= jQuery(window).height() - 190 && !canSeeMenu) {
			jQuery(".box-scroll").css("top", "");
			jQuery(".header-sidebar").css("height", "");
			jQuery(".box-scroll").css("height", "");
			canSeeMenu = true;
		}
	});
	if(jQuery(window).width()>=768) {
		var canSee = true;
		jQuery(window).scroll(function() {
			if (jQuery(this).scrollTop() > 5 && canSee) {
				jQuery(".header h1.logo").animate({top:"-=20px"});
				canSee = false;
				jQuery('#nav .collection').addClass('active');
				jQuery("#overlay-top").bgStretcher.pause();
				window.history.pushState("object or string", "Collection", "/collection");
			} else if (jQuery(this).scrollTop() <= 5 && !canSee) {
				jQuery(".header h1.logo").animate({top:"+=20px"});
				jQuery('#nav .collection').removeClass('active');
				jQuery("#overlay-top").bgStretcher.play();
				window.history.pushState("object or string", "Home", "/");
				canSee = true;
			}
		});
	};


	    
	
var isiPad = navigator.userAgent.toLowerCase().indexOf("ipad");
	if(isiPad > -1)
	{
		// Catch touchevents on dont_scroll-objects
		jQuery('body').bind("touchmove", {}, function(event){
		  event.preventDefault();
		});
	}

	if(isiPad > -1)
	{
		jQuery("#godown").attr("href", "/collection");
		jQuery("#godown").removeClass( "scrolldown" );
	}else{
		jQuery(window).scroll(function () {
			if (jQuery(this).scrollTop() > 224) {
						jQuery("#godown").removeClass( "scrolldown" );
					} else {
						jQuery("#godown").addClass( "scrolldown" );
			}
		});
		jQuery(function() {
						jQuery('.scrolldown').on('click',function(event){
							jQuery('html, body').stop().animate({scrollTop: jQuery('.category-products').offset().top - 50}, 800,'linear');
							return false;
						});
		});
	};

jQuery(window).scroll(function () {
	if (jQuery(this).scrollTop() > 50) {
		jQuery(".overlay-pagination").fadeOut(1000);
	} else {
		jQuery(".overlay-pagination").fadeIn(1000);
	}
});
		 
jQuery(document).ready(function(){
	resizeNavSlide();
	window.onresize = function(event) {
			resizeNavSlide();
	}
});
				
				
function resizeNavSlide() {
var navHeight = jQuery('.nav-container').outerHeight(true);
	if (navHeight ==  192) {
		jQuery("#wrap").css("margin-top", "-23px");
	}
	else if (navHeight ==  168) {
		jQuery("#wrap").css("margin-top", "");
	}
	else {
		jQuery("#wrap").css("margin-top", "");
	}
};
jQuery(function() {
	jQuery('#close-cart').bind('click',function(event){
		resizeNavSlide();
	});
});