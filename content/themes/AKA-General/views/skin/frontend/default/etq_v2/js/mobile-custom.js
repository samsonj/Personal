jQuery(document).ready(function(){
		if(jQuery(window).width()<=767) {
			function doOnOrientationChange()
			 {
				switch(window.orientation) 
				{  
					case -90:                 
						document.getElementById("landscape").style.display="block";
					break;
					case 90:              
						document.getElementById("landscape").style.display="block";                 
					break;
					default:                   					
						jQuery('#landscape').fadeOut("slow", function () {
							jQuery('#landscape').css({display:"none"});
						});
						//document.getElementById("landscape").style.display="none";
					break;                            
				}
			}
			jQuery(document).ready(doOnOrientationChange());
			window.addEventListener('orientationchange', doOnOrientationChange);  		
		};
		if(jQuery(window).width()<=767) {
			var lastScrollTop = 0;
			jQuery(window).scroll(function(){
				var st = jQuery(this).scrollTop();
				if (st > lastScrollTop){
					if(jQuery('.header-container').data('size') === 'big')
					{
						jQuery('.header-container').data('size','small');
						jQuery('.header-container').stop().animate({top:'-100px'},1000);
					}
				}
				else
				{
					if(jQuery('.header-container').data('size') === 'small')
					{
						jQuery('.header-container').data('size','big');
						jQuery('.header-container').stop().animate({top:'0px'},1000);
					}  
				}
				lastScrollTop = st;
			});
		}
			
		jQuery("#mobnav").click(function(){
		
			var touchScroll = function( event ) {
				return event.preventDefault();
				return false;
			};
		
			//switch icons upon clicking 	
            if (jQuery('#mobicon').hasClass('mobnav-open-icon')){
				jQuery('#mobicon').removeClass( "mobnav-open-icon" ).addClass( "mobnav-close-icon" );
			}
			else if (jQuery('#mobicon').hasClass('mobnav-close-icon')){
				jQuery('#mobicon').removeClass( "mobnav-close-icon" ).addClass( "mobnav-open-icon" );
			}
			
			if (jQuery(this).hasClass('active')) {
				jQuery(document).unbind( "touchmove" );
				//close menu
				jQuery(this).removeClass('active');  
	
				jQuery('#wrap, .footer-container, #overlay-bg').css('-webkit-transform', 'translate3d(0px,0px,0px)');
				jQuery('#wrap, .footer-container, #overlay-bg').css('-moz-transform', 'translate3d(0px,0px,0px)');
				jQuery('#wrap, .footer-container, #overlay-bg').css('-o-transform', 'translate3d(0px,0px,0px)');
				jQuery('#wrap, .footer-container, #overlay-bg').css('transform', 'translate3d(0px,0px,0px)');
				return false;
			} else {
				jQuery(document).bind( 'touchmove', touchScroll );
				
				//Slide  To Mobile Menu
				jQuery(this).addClass('active');
				
				//jQuery("#nav-left").css("z-index", "3");			
				jQuery('#wrap, .footer-container, #overlay-bg').css('-webkit-transform','translate3d(0px,75px,0)');
				jQuery('#wrap, .footer-container, #overlay-bg').css('-moz-transform', 'translate3d(0px,75px,0)');
				jQuery('#wrap, .footer-container, #overlay-bg').css('-o-transform', 'translate3d(0px,75px,0)');
				jQuery('#wrap, .footer-container, #overlay-bg').css('transform', 'translate3d(0px,75px,0)');
				
				//Close Left slide if opened
				jQuery('#nav-left').css('-webkit-transform', 'translate3d(0px,0,0)');
				jQuery('#nav-left').css('-moz-transform', 'translate3d(0px,0,0)');
				jQuery('#nav-left').css('-o-transform', 'translate3d(0px,0,0)');
				jQuery('#nav-left').css('transform', 'translate3d(0px,0,0)');

				jQuery('html, body').animate({scrollTop:0}, 'slow');
				return false;
			}
		});
		
		jQuery(function() {
		
			var touchScroll = function( event ) {
				return event.preventDefault();
				return false;
			};
			jQuery('#mobcart').bind('click',function(event){
			
					if (jQuery('#mobcart').hasClass('active')){
						jQuery(document).unbind( "touchmove" );
						jQuery('#mobcart').removeClass('active');
						
											jQuery('#wrap, .footer-container, #overlay-bg').css('-webkit-transform', 'translate3d(0px,0,0)');
											jQuery('#wrap, .footer-container, #overlay-bg').css('-moz-transform', 'translate3d(0px,0,0)');
											jQuery('#wrap, .footer-container, #overlay-bg').css('-o-transform', 'translate3d(0px,0,0)');
											jQuery('#wrap, .footer-container, #overlay-bg').css('transform', 'translate3d(0px,0,0)');
						
					}else{
					jQuery(document).bind( 'touchmove', touchScroll );
						jQuery('#mobcart').addClass('active');
											jQuery("#nav-left").css("z-index", "0");
											jQuery('#wrap, .footer-container, #overlay-bg').css('-webkit-transform', 'translate3d(-266px,0,0)');
											jQuery('#wrap, .footer-container, #overlay-bg').css('-moz-transform', 'translate3d(-266px,0,0)');
											jQuery('#wrap, .footer-container, #overlay-bg').css('-o-transform', 'translate3d(-266px,0,0)');
											jQuery('#wrap, .footer-container, #overlay-bg').css('transform', 'translate3d(-266px,0,0)');
											jQuery('html, body').animate({scrollTop:0}, 'slow');
					}
				return false;
			});

		});
	
		jQuery(function() {
			jQuery('#btnopencart').bind('click',function(event){
				jQuery("#nav-left").css("z-index", "0");
			});
		});
	
		jQuery(function() {
			jQuery('#close-cart').bind('click',function(event){
				if (jQuery('#mobcart').hasClass('active')){
						jQuery(document).unbind( "touchmove" );
						jQuery('#mobcart').removeClass('active');
					}else{
						jQuery('#mobcart').addClass('active');
				}
			});
		});

});