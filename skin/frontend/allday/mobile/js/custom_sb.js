
jQuery(document).ready(function($){

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

 $('.bxslider-pr-view').slick({
      slidesToShow: 1,
      slidesToScroll: 1
  });


  // refill remainder popup
    $(".popup_refill, .popup_refill_overlay").hide();

    $(".refilreminder-action").live('click', function(){ 
      $(".popup_refill, .popup_refill_overlay").show();
    });

    $(".popup_refill_overlay, .popup_refill_close").live('click', function(){  
      $(".popup_refill, .popup_refill_overlay").hide();
    });

  });
