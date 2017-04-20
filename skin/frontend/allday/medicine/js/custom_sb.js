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

  //opc msg
  // $(".opc-messages-action").live('click', function(){  
  //  // alert("hi");
  //  $(" .cashbackCoupon_overlay").hide();
  // });
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
  });