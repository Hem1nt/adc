jQuery(document).ready(function($){
    $(".all-categories").click(function(){
        $(".all_categories_list").stop( true, true ).toggle();
        $(".block-compare, .block-account .block-content, .block-layered-nav").hide();
    });

    $(".mobile-pr-category-icon").click(function(){
        $(".all_categories_list").stop( true, true ).toggle();
        var search_status = $(".all_categories_list").css("display");
        if (search_status == "none")
        {
          $(".overlay-header").hide();
        }
        else
        {
          $(".overlay-header").show();
        }
        $(".quick-access, .block-layered-nav, .block-compare, .logged-in-user .dropdown_links, .block-account .block-content").hide();
        $(".logged-in-user").removeClass("active");
        $(".logged-in-user, .logged-in-user .dropdown_links").removeClass("visible");
    });

    $(".mobile-pr-search-icon").click(function(){
        $(".quick-access").stop( true, true ).toggle();
        var search_status = $(".quick-access").css("display");
        if (search_status == "none")
        {
          $(".overlay-header").hide();
        }
        else
        {
          $(".overlay-header").show();
        }
        $(".all_categories_list, .logged-in-user .dropdown_links, .block-layered-nav, .block-compare, .block-account .block-content").hide();
        $(".logged-in-user").stop( true, true ).removeClass("active");
        $(".logged-in-user, .logged-in-user .dropdown_links").removeClass("visible");
    });

    $(".link-notify").click(function(e){
        e.preventDefault();
        var parent_notify = $(this).closest( "li" );
        $(".notify-overlay").stop( true, true ).toggle();
        $(parent_notify).find(".notify-me-boxwrapper").stop( true, true ).toggle();
    });

    $(".pr-link-notify").click(function(e){
        e.preventDefault();
        $(".notify-overlay").stop( true, true ).toggle();
        $(".pr-notify-me-boxwrapper").stop( true, true ).toggle();
    });

    $(".notify-overlay").click(function(){
        $(".notify-me-boxwrapper").hide();
        $(".pr-notify-me-boxwrapper").hide();
        $(".notify-overlay").hide();
    });

    $(".fiter-icon").click(function(){
        $(".block-layered-nav").stop( true, true ).toggle();
        $(".block-compare").hide();
        $(".all_categories_list").hide();
        var test = $(window).width();
        if(test<=736){
          $(".quick-access, .logged-in-user .dropdown_links").hide();
          $(".logged-in-user").stop( true, true ).removeClass("active");
          $(".logged-in-user, .logged-in-user .dropdown_links").removeClass("visible");
        }
    });

    $(".overlay_div").click(function(){
      $(this).hide();
    });

    $(".compare-icon").click(function(){
        $(".block-compare").stop( true, true ).toggle();
        $(".block-layered-nav, .all_categories_list").hide();
          var test = $(window).width();
          //alert(test);
        if(test<=736){
          $(".quick-access, .logged-in-user .dropdown_links").hide();
          $(".logged-in-user").stop( true, true ).removeClass("active");
          $(".logged-in-user, .logged-in-user .dropdown_links").removeClass("visible");
        }

    });

    $(".my-account-icon").click(function(){
        /*$(".block-account .block-content").toggle();*/
        var account_status = $(".block-account .block-content").css("display");
        //alert(account_status);
        $(".block-compare, .all_categories_list").hide();
        var test = $(window).width();
          //alert(test);
        if(test<=736){

          $(".my-account-icon").toggleClass("down");
          $(".block-layered-nav, .all_categories_list, .quick-access, .logged-in-user .dropdown_links").hide();
          $(".logged-in-user").stop( true, true ).removeClass("active");
          $(".logged-in-user, .logged-in-user .dropdown_links").removeClass("visible");

          if (account_status == "block") {
            $(".overlay-header").hide();
            $(".block-account .block-content").hide();
          } else {
            $(".overlay-header").show();
            $(".block-account .block-content").show();
          }
        }

        else {
          $(".block-account .block-content").toggle();
        }

        
            
    });

    $(".compare-acc-icon").click(function(){
        $(".block-compare").stop( true, true ).toggle();
        $(".block-account .block-content, .all_categories_list").hide();
        var test = $(window).width();
          //alert(test);
        if(test<=736){
          $(".block-layered-nav, .all_categories_list, .quick-access, .logged-in-user .dropdown_links").hide();
          $(".logged-in-user").stop( true, true ).removeClass("active");
          $(".logged-in-user, .logged-in-user .dropdown_links").removeClass("visible");
        }
    });

    $(window).resize(function() {
      /*if ($(window).width() <= 736) {
       $(".my-account-icon, .mobile-pr-search-icon").addClass('pr-mobile');
      }
      else {
        $(".my-account-icon, .mobile-pr-search-icon").removeClass('pr-mobile');
      }*/
    });

    $(".notify-close-btn").click(function(){
        $(".notify-me-boxwrapper, .pr-notify-me-boxwrapper, .notify-overlay").hide();
    });

    $("#outer_ul").mCustomScrollbar({
      axis:"y",
      theme:"light-3"
    });

    // $(".logged-in-user").hover(function(){
    //  $(this).stop( true, true ).toggleClass("active");
    //  $(".dropdown_links").stop( true, true ).toggle();
    // });

    var w_width = $(window).width();
    if(w_width<=840){
      $(".block-account .block-content").hide();
      
    }
    else {
      $(".block-account .block-content").show();
      
    }

    $(window).resize(function(){
      var w_width = $(window).width();
      if(w_width<=840){
        $(".block-account .block-content").hide();
        
      }
      else {
        $(".block-account .block-content").show();
        
      }
    });

    $(".logged-in-user_normal").click(function(){
      $(".logged-in-user").stop( true, true ).toggleClass("active");
      // $(".logged-in-user .dropdown_links").stop( true, true ).toggle();
      $(".logged-in-user, .logged-in-user .dropdown_links").toggleClass("visible");
      var test = $(window).width();
          //alert(test);
        if(test<=736){
          var search_status = $(".logged-in-user .dropdown_links").css("display");
            if (search_status == "none")
            {
              // alert("none");
              $(".overlay-header").hide();
              $(".logged-in-user .dropdown_links").removeClass("visible");
              $(".logged-in-user").stop( true, true ).removeClass("active");
            }
            else
            {
              // alert("block");
              $(".overlay-header").show();
            }
          $(".block-layered-nav, .all_categories_list, .quick-access, .block-compare").hide();
          $(".block-account .block-content").hide();
          }
    });



    $(".top-link-cart").hover(function(){
       $(".inner-wrapper").stop( true, true ).slideDown("fast");
       $(".min_cart_overlay").stop( true, true ).fadeIn("fast");
    });


    $(".min_cart_overlay").hover(function(){
       $(".inner-wrapper").stop( true, true ).hide("fast");
       $(".min_cart_overlay").stop( true, true ).fadeOut("fast");
    });

    $("#search").focus(function(){
       $(".block-compare, .all_categories_list, .block-account .block-content, .block-layered-nav").hide();
    });

    

    // $('.cartpage-slider').bxSlider({
    //   minSlides: 1,
    //   maxSlides: 2,
    //   infiniteLoop :false,
    //   hideControlOnEnd: true,
    //   slideWidth: 360,
    //   slideMargin: 0,
    //   moveSlides: 1
    // });

    $('.qty_of_product').change(function(){
      if($(this).prev().val() == 0){
        alert('Please enter valid quantity Greater than 0');
      }
      else{
        $('.btn-update').trigger('click');
      }
      //$(this).closest("form").submit();
    });
    
    /*jQuery(window).load(function(){
      var td_ht = $(".checkout-cart-index table#shopping-cart-table td:first-child").outerHeight();
      console.log(td_ht);      
    })*/

    $(".tab-link").each(function(){
      if($(this).hasClass("current")){
        var get_val = $(this).html();
        $(".product_mobile_tab .ptitle").html(get_val);
      }
    })

    $(".product_mobile_tab").click(function(){
       $(".tabs").stop( true, true ).toggleClass('visible');
       $(".menu_toogle_arrow").toggleClass("menu_toogle_arrow_up");
    });

    $(".tab-link").click(function(){
      var cat_title = $(this).html();
      $(".ptitle").html(cat_title);
      $(".tabs").removeClass('visible');
      $(".menu_toogle_arrow").toggleClass("menu_toogle_arrow_up");
    });


    $(".select_method").live("click", function(){
      //alert(234234);
       $(this).next(".payment_tabs").toggleClass("show_methods");
       $(this).toggleClass("opened");
    });

    $(".medical_nextbtn").live("click", function(){     
      $(".payment_tabs_area dd ul").each(function(){
        var display_status = $(this).css("display");
        if(display_status == "block"){
          var onload_method = $(this).find("h5").text();
            $(".select_method").text(onload_method);
        }
      });
    });


    $(".payment_tabs dt").live("click", function(){
      var method_name = $(this).find("label").html();
      $(".select_method").html(method_name);
      $(".select_method").removeClass("opened");
      $(this).parent().removeClass("show_methods");
    });

    if($(".my-account").length > 0){
      $("body").addClass('my_account_col');
    }

    if($(".rewardpoints-index-points").length > 0){
      $("body").addClass('my_account_col');
    }

    if($(".rewardpoints-index-referral").length > 0){
      $("body").addClass('my_account_col');
    }

    if($(".importorder-index-index").length > 0){
      $("body").addClass('my_account_col');
    }

    if($(".refillreminder-view-index").length > 0){
      $("body").addClass('my_account_col');
    }

    if($(".trackorder-index-index").length > 0) {
      $("body").addClass('my_account_col');
      $("body").addClass('sales-order-view');      
    }


    function sticky_header(){
      var window_width = $(window).width();

      if(window_width >=737){
        var topm = $(".header").offset().top;
        var top_ht = $(".fix-header-block").css("height");

          $(window).scroll(function(){
            scroll = $(window).scrollTop();
            if (scroll >= topm) 
            {
              $(".fix-header-block").addClass('sticky');
              $(".header-top-block").hide();
              $(".main-container").css("marginTop", top_ht);
            }
            else {
              $(".fix-header-block").removeClass('sticky');
              $(".header-top-block").show();
              $(".main-container").css("marginTop", "0px");
              $(".my_account_col .main-container").css("marginTop", "20px");
              $(".customer-account-forgotpassword .main-container").css("marginTop", "20px");
              $(".customer-account-login .main-container").css("marginTop", "20px");
              $(".contacts-index-index .main-container").css("marginTop", "20px");
              $(".customer-account-create .main-container").css("marginTop", "20px");
              $(".checkout-onepage-success .main-container").css("marginTop", "20px");
            }
          });

      }
      else{
        $(window).scroll(function(){
          $(".fix-header-block").removeClass('sticky');
          $(".header-top-block").show();
          $(".main-container").css("marginTop", "0px");
          $(".my_account_col .main-container").css("marginTop", "20px");
          $(".customer-account-resetpassword .main-container").css("marginTop", "20px");
          $(".checkout-onepage-success .main-container").css("marginTop", "20px");
          $(".trackorder-index-index .main-container").css("marginTop", "20px");
      });
      }

    }

    $(window).load(function(){

      // sticky_header();
      
    });


    $(window).resize(function(){

      // sticky_header();
      
    });

    $(".overlay-header").click(function(){
      $(".quick-access, .all_categories_list, .overlay-header, .block-account .block-content").hide();
      $(".logged-in-user, .logged-in-user .dropdown_links").removeClass("visible");
      $(".logged-in-user").removeClass("active");
    });

  //touch/mobile detection
    /*if (
      navigator.userAgent.match(/Phone/i) ||
      navigator.userAgent.match(/DROID/i) ||
      navigator.userAgent.match(/Android/i) ||
        navigator.userAgent.match(/webOS/i) ||
        navigator.userAgent.match(/iPhone/i) ||
        navigator.userAgent.match(/iPod/i) ||
        navigator.userAgent.match(/BlackBerry/) || 
        navigator.userAgent.match(/Windows Phone/i) || 
        navigator.userAgent.match(/ZuneWP7/i) || 
        navigator.userAgent.match(/IEMobile/i)
    ){ var mobile_device = true; var touch_device = true; }*/
     
    //touch/tablet detection
    /*if (
      navigator.userAgent.match(/Tablet/i) ||
        navigator.userAgent.match(/iPad/i) ||
        navigator.userAgent.match(/Kindle/i) ||
        navigator.userAgent.match(/Playbook/i) ||
        navigator.userAgent.match(/Nexus/i) ||
        navigator.userAgent.match(/Xoom/i) ||
        navigator.userAgent.match(/SM-N900T/i) || //Samsung Note 3
        navigator.userAgent.match(/GT-N7100/i) || //Samsung Note 2
        navigator.userAgent.match(/SAMSUNG-SGH-I717/i) || //Samsung Note
        navigator.userAgent.match(/SM-T330NU/i) //Samsung Tab 4
     
    ){ var tablet_device = true; var touch_device = true; }*/
     
    //get ready
     
    /*if(tablet_device || touch_device){  
      $("body").addClass("tab-max-width");
    }*/
     
    /*if(tablet_device){  
      $("#device_icon").addClass("icon-tablet-landscape");
      $("#user_agent").html(navigator.userAgent);
    }
     
    if(!mobile_device && !tablet_device){
      $("#device_icon").addClass("icon-monitor");
      $("#user_agent").html(navigator.userAgent);
    }*/
 
//eof ready

        jQuery("#testimonial-form").submit(function() {
            if(jQuery('input:radio').is(':checked')) {
                jQuery('.radio_checked').css('display','none');
            }  else {
                jQuery('.radio_checked').css('display','block');
            }
        });

        jQuery('input:radio').change(
            function(){

                var value = jQuery(this).val();
                jQuery('#ratingBox .css-label').removeClass('active');
                for (i = 1; i <= value; i++) {
                     var id = jQuery(":radio[value="+i+"]").attr("id");
                        jQuery("."+id).addClass('active');
                }
            }
        );

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