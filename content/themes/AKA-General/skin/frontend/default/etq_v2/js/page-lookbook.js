jQuery(document).ready(function() {
			jQuery.fn.fullpage({
				verticalCentered: false,
				resize : true,
				scrollingSpeed: 1100,
				easing: 'easeOutQuad',
				navigation: false,
				css3: false,
				//fixedElements: '#section0',
				/*'afterLoad': function(anchorLink, index){
					if(index == 2){
						jQuery('#section3, #section3, #section3').addClass('active');
					}
					jQuery('#infoMenu').toggleClass('whiteLinks', index ==4);
					
				},*/
				
				'onLeave': function(index, direction){
					if (direction == 'down'){
										jQuery('.section').eq(index -1).removeClass('moveDown').addClass('moveUp');
					}
					else if(direction == 'up'){
									jQuery('.section').eq(index -1).removeClass('moveUp').addClass('moveDown');
					}
					if (index == 5 && direction == 'down' )
					{
					jQuery("#wrap").animate({bottom: "370px"}, 500)
					}
					if (index == 6 && direction == 'up' )
					{
					jQuery("#wrap").animate({bottom: "0px"}, 500)
					}
					//jQuery('#wrap').toggleClass('active-lookbook', (index == 5 && direction == 'down' ) || (index == 4 && direction == 'up'));
				},
				
				afterRender: function(){
					//jQuery('.box-scroll').appendTo('body');
					//jQuery('.box-logo').appendTo('body');
				}
			});
			
				jQuery(".slide-arrow-nav.down" ).click(function() {
					  jQuery.fn.fullpage.moveSlideDown();
				});
				
				jQuery(".slide-arrow-nav.up" ).click(function() {
						jQuery.fn.fullpage.moveSlideUp();
				});
});