jQuery(document).ready(function(){
  jQuery('#outer_ul ul').css("display","none");
  jQuery("#parent").live("click",function(event){
  jQuery(".block").removeAttr("style");
  jQuery(".block").removeAttr("style");
  jQuery(".col-left").removeAttr("style");
  if(false == jQuery(this).next().is(':visible')) {           
        jQuery(".block").removeAttr("style");
        jQuery(".block").removeAttr("style");
        jQuery(".col-left").removeAttr("style");
        jQuery(this).closest('ul').find('ul').slideUp(300);
        jQuery(this).removeAttr("class");
        jQuery(this).closest('ul').find('em').css("background-position",'0 9px');
    }
    jQuery(this).toggleClass('expanded');  
    jQuery(this).next().slideToggle(300);     
    if(jQuery(this).attr("class") == "expanded") {
        jQuery(this).css("background-position",'0 -11px');
    } else {
        jQuery(this).css("background-position",'0 9px');
    }
    
});
 
jQuery(".current").show();
jQuery(".current").prev().addClass("expanded");
jQuery('.current').prev().css("background-position",'0 -11px');
});
