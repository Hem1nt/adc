// custom_sb

jQuery(document).ready(function($){
  //cashback coupon
  $(".cashbackCoupon, .cashbackCoupon_overlay").hide();

  $(".show-coupon").live('click', function(){ 
    $(".cashbackCoupon, .cashbackCoupon_overlay").show();
  });

  $(".cashbackCoupon_overlay").live('click', function(){  
    $(".cashbackCoupon, .cashbackCoupon_overlay").hide();
  });

  $(".close-message-wrapper").click(function(){
    $(".cashbackCoupon_overlay").trigger('click');
  });

  //click to call popup
 // $(".modal-content, .click_tocall_overlay").hide();

  $("#customBtn").live('click', function(){
    $(".modal-content, .click_tocall_overlay").show();
  });

  $(".click_tocall_overlay, .close_btn").live('click', function(){  
    $(".modal-content, .click_tocall_overlay").hide();
  });

  //opc msg
  // $(".opc-messages-action").live('click', function(){  
  //  // alert("hi");
  //  $(" .cashbackCoupon_overlay").hide();
  // });

    // if( $('.trustpilot_reviews_inner').hasClass('companyReply') ) {
    // // alert(123);
    //   $('.review_wrap').addClass('sonal');
    //  }



     if ($('.trustpilot_reviews_inner').find('companyReply')) { 
        $('.review_wrap').addClass('123456777'); 
        // console.log("true");
    }


    // refill remainder popup
    $(".popup_refill, .popup_refill_overlay").hide();

    $(".refilreminder-action").live('click', function(){ 
      $(".popup_refill, .popup_refill_overlay").show();
    });

    $(".popup_refill_overlay, .popup_refill_close").live('click', function(){  
      $(".popup_refill, .popup_refill_overlay").hide();
    });

});

jQuery(document).ready(function($){
    $(".cat_list").click(function(){
    $(".category_list_area1").stop( true, true ).toggle();
    var screen_width = $(window).width();
        if(screen_width<=736){
          $(".block-account .block-content").removeClass("dropdwn");
          $(".header").hide();
          $(".logged-in-user, .logged-in-user .dropdown_links").removeClass("visible");
          var search_status = $(".category_list_area1").css("display");
          if (search_status == "none")
          {
            $(".mob-overlay").hide();
          }
          else
          {
            $(".mob-overlay").show();
          }
        }
      });

    $(".scroll_div_content").mCustomScrollbar();

     $('.bxslider-pr-view').slick({
          slidesToShow: 4,
          slidesToScroll: 1,
          responsive: [
            {
              breakpoint: 1025,
              settings: {
                slidesToShow: 3,
              }
            },
            {
              breakpoint: 737,
              settings: {
                slidesToShow: 1,
                slidesToScroll: 1
              }
            }
          ]
      });

  equalheight = function(container){
   var currentTallest = 0,
        currentRowStart = 0,
        rowDivs = new Array(),
        el,
        topPosition = 0;
    jQuery(container).each(function() {

      el = jQuery(this);
      jQuery(el).height('auto')
      topPostion = el.position().top;

      if (currentRowStart != topPostion) {
        for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
          rowDivs[currentDiv].height(currentTallest);
        }
        rowDivs.length = 0; // empty the array
        currentRowStart = topPostion;
        currentTallest = el.height();
        rowDivs.push(el);
      } else {
        rowDivs.push(el);
        currentTallest = (currentTallest < el.height()) ? (el.height()) : (currentTallest);
     }
      for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
        rowDivs[currentDiv].height(currentTallest);
      }
   });
 } 
 
   equalheight('ul.products-grid li.item');

   $(window).on('resize', function(){
     equalheight('ul.products-grid li.item');
   });



  // if (!$("body").hasClass("opc-index-index") == true) {
      // jQuery.fn.snow({minSize: 15, maxSize: 25, newOn: 500, flakeColor: '#bbb' });/*minSize: 5, maxSize: 25, newOn: 1000, */
      // jQuery.fn.snow1({minSize: 10, maxSize: 20, newOn: 400, flakeColor: '#bbb' });/*minSize: 5, maxSize: 25, newOn: 1000, */

  // }
   
   // $("body").addClass("adc_christmas");
   // $("body").addClass("new_year");
   
  });