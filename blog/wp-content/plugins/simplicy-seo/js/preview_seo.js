function showHTML () {

    // ref textarea
    textarea1 = document.getElementById('seo_desc_post_code');
    // where to view HTML code
    viewHtml = document.getElementById('desc-seo');
    // Finally get HTML output in div
    viewHtml.innerHTML = textarea1.value ;
	
	// ref textarea
    textarea2 = document.getElementById('seo_title_sp');
    // where to view HTML code
    viewHtml = document.getElementById('title-seo');
    // Finally get HTML output in div
    viewHtml.innerHTML = textarea2.value ;
	
	// ref textarea
    textarea3 = document.getElementById('seo_title_code');
    // where to view HTML code
    viewHtml = document.getElementById('title-seo');
    // Finally get HTML output in div
    viewHtml.innerHTML = textarea3.value ;
	
	// ref textarea
    textarea4 = document.getElementById('seo_desc_code');
    // where to view HTML code
    viewHtml = document.getElementById('desc-seo');
    // Finally get HTML output in div
    viewHtml.innerHTML = textarea4.value ;

}

jQuery(document).ready(function() {
 // hides the slickbox as soon as the DOM is ready
  jQuery('#toggle-seo-1').hide();
  jQuery('#toggle-seo-2').hide();

 // toggles the slickbox on clicking the noted link
  jQuery('a#slick-slidetoggle').click(function() {
	jQuery('#toggle-seo-1').slideToggle(600);
	return false;
  });
  jQuery('a#slick-seo-2').click(function() {
	jQuery('#toggle-seo-2').slideToggle(600);
	return false;
  });
});