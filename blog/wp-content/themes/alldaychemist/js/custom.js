(function($) {
	$(document).ready( function() {
	    
	    $('.mobile-pr-category-icon').click(function(e) {
	    	var w_width = $(window).width();
          	//alert(test);
	        if(w_width<=1024){
	          $('aside#categories-2').toggle();
	        }
	    	
	    });

	});
})(jQuery);

jQuery(document).ready(function(){
		jQuery('.wp_rp_wrap').hide(); 
  jQuery(window).scroll(function () {     
      var scroll = jQuery(window).scrollTop();
      if(scroll >= '10'){
      	if(jQuery('.wp_rp_wrap').hasClass('scroll_available')){
	        jQuery("body").addClass("reduce_content");

	        if(scroll >= '500'){
		        jQuery('.wp_rp_wrap').show(); 
		    }
		    if(scroll <= '500'){
		        jQuery('.wp_rp_wrap').hide(); 	        
		    }
		}

      }

      if(scroll <= '9'){
      	if(jQuery('.wp_rp_wrap').hasClass('scroll_available')){
	        jQuery("body").removeClass("reduce_content");
	    }

      }
  });

   jQuery(".toggle_img").click(function(){
   	// alert("hi");
        // jQuery(".wp_rp_content").slideToggle();
        jQuery(".wp_rp_content").toggleClass("hide_area","slow");
        jQuery(this).toggleClass("arrow_toggle","slow");
        jQuery(this).closest('.wp_rp_wrap').removeClass('scroll_available','slow');
        // jQuery('.toggle_img').hide();

    });
});