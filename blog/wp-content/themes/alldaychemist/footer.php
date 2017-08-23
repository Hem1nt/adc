<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?>

	</div><!-- #main -->

	<footer id="colophon" role="contentinfo">

			<?php
				/* A sidebar in the footer? Yep. You can can customize
				 * your footer with three columns of widgets.
				 */
				if ( ! is_404() )
					get_sidebar( 'footer' );
			?>
			<div class="footer-container">
			<div class="footer">
        <div class="adc-footer-row1"><span class="footer-logo-adc">&nbsp;</span>
<div class="adc-footer-links">
<ul>
<li><a href="http://www.alldaychemist.com/content/4-aboutus">About Us</a></li>
<li><a href="http://www.alldaychemist.com/contacts/">Contact Us</a></li>
</ul>
<ul>
<li><a href="http://www.alldaychemist.com/content/7-disclaimer">Disclaimer</a></li>
<li><a href="http://www.alldaychemist.com/content/3-terms-and-conditions">Terms And Conditions</a></li>
</ul>
<ul>
<li><a href="http://www.alldaychemist.com/cancellation-policy">Cancellation Policy</a></li>
<li><a href="http://www.alldaychemist.com/refunds-and-returns">Refunds and Returns</a></li>
</ul>
<ul>
<li><a href="http://www.alldaychemist.com/sitemap">Site Map</a></li>
<li><a href="http://www.alldaychemist.com/generic_drugs">Drug Policy</a></li>
</ul>
<ul class="last">
<li><a href="http://www.alldaychemist.com/know-about-indian-pharmacy">Know about Indian Pharmacy</a></li>
<li><a href="http://www.alldaychemist.com/details-on-indian-pharmacies">Details on Indian Pharmacies</a></li>
</ul>
</div>
</div><div class="footer-our-partners">
	<span class="our-partner-text">Our Partners:</span>
	<span class="op1"></span>
	<span class="op2"></span>
	<span class="op3"></span>
</div>

<div class="footer_imp_msg">
	<div>
		<p class="footer_content">Please note that not all medications, including any referenced on this page, are dispensed from our affiliated Indian pharmacy. The medications in your order may be filled and shipped from an approved International fulfilment center located in a country other than India. In addition to dispensing medications from our Indian pharmacy, medication orders are also filled and shipped from international fulfilment centers that are approved by the regulatory bodies from their respective countries.  
			<br><br>
		Medication orders are filled and shipped from approved fulfilment centers around the world including, but not limited to, India, United Kingdom, New Zealand, Mauritius and the United States. The items in your order may be filled and shipped from any one of the above jurisdictions. The products are sourced from various countries as well as those listed above. All of our affiliated fulfilment centers have been approved by the regulatory bodies from their respective countries.	
			<!-- <a href="#show_footer_txt_readmore" class="show_footer_txt" id="footer_readmore">Read More</a> -->
		</p>
		
		<!-- <p id="show_footer_txt_readmore">
			Medication orders are filled and shipped from approved fulfilment centers around the world including, but not limited to, India, United Kingdom, New Zealand and the United States. The items in your order may be filled and shipped from any one of the above jurisdictions. The products are sourced from various countries as well as those listed above. All of our affiliated fulfilment centers have been approved by the regulatory bodies from their respective countries.
		<a href="#footer_imp_msg" class="show_footer_txt" id="footer_less">Less</a>
		</p> -->
	</div>	
</div>
<style type="text/css">
.footer .footer_links_area .footer_imp_msg p{text-align: justify;text-indent: 0px;color: #333;}
.footer .footer_links_area .footer_imp_msg{border: 1px solid #ccc;padding: 10px 10px 10px 10px;}
.footer .footer_links_area .footer_imp_msg .show_footer_txt{color: #819d01;}
.footer_imp_msg{margin-bottom:10px; }
.show_footer_txt{font-weight: bold;margin-left:5px;}
/*.footer_content{margin-bottom:10px; }*/
</style>

<script type="text/javascript">
jQuery(document).ready(function () {
	jQuery('#show_footer_txt_readmore').hide();
	jQuery('.show_footer_txt').click(function () {
		jQuery('#show_footer_txt_readmore').slideDown( "slow");
		jQuery('.footer_content').css({'margin-bottom':'10px'});
		jQuery('#footer_readmore').hide();
		jQuery('#footer_less').show();
		
	});

	jQuery('#footer_less').click(function () {
		jQuery('#show_footer_txt_readmore').slideUp( "slow");
		jQuery('#footer_readmore').show();
		jQuery('#footer_less').hide();
		jQuery('.footer_content').css({'margin-bottom':'0px'});
	});
});

</script>	
 <div id="toTop" style="width: 35px;">

    <div class="subscribe" style="display: none;">
        <div class="subscribe-innerdiv">
        Subscribe to our Newsletter <span class="xtra-txt">&amp; never miss a latest product or offer</span>
        <div class="subscribe-input-go">
	        <input type="text" id="txtEmailDiv" placeholder="Please enter E-mail address" class="subs" onblur="return showText(this, 'Please enter E-mail address')" onfocus="return hideText(this, 'Please enter E-mail address')">
	        <input type="button" id="subscribebtn" value="Go">
       </div>
    </div>

</div>
<div class="subscribe_msg" style="display:none;">
 <div class="subscribe-innerdiv">Thank you for Subscribing us.</div>
</div>
	<a id="show_subs" class="subs_a" onclick="" style="bottom: 15px; color: rgb(255, 255, 255); position: fixed; left: 4px; text-decoration: none; border-radius: 3px; border: 1px solid rgb(255, 255, 255); padding: 0px 6px;">&gt;</a>
    <a id="close_subs" class="subs_a" onclick="" style="bottom: 15px; color: rgb(255, 255, 255); position: fixed; left: 8px; text-decoration: none; border-radius: 3px; border: 1px solid rgb(255, 255, 255); padding: 0px 6px; display: none;">X</a>
</div>

        <!-- <p class="bugs"> - <a href="http://www.magentocommerce.com/bug-tracking" onclick="this.target='_blank'"><strong></strong></a> </p> -->
        <address>© <?php echo date('Y');?> AllDayChemist. All Rights Reserved.</address>
    </div>
</div>
	</footer><!-- #colophon -->
	
</div><!-- #page -->
<!-- bookmark start -->
<div style="position: fixed; right: 0px; top: 0px; z-index:100" class="page-bookmrk">
	<a href="#" onclick="return add_favorite(this);">
		<img class="beforeScroll" width="120px" src="<?php echo get_site_url(); ?>/wp-content/themes/alldaychemist/images/bookmark_add.png" />
		<img class="afterScroll" width="58px" src="<?php echo get_site_url(); ?>/wp-content/themes/alldaychemist/images/bookmark_fixed.png" />
	</a>
</div>
<script type="text/javascript">
   function add_favorite( a ) { 
  title = document.title; 
  url = document.location; 
  try { 
    // Internet Explorer 
    window.external.AddFavorite( url, title ); 
  } 
  catch (e) { 
    try { 
      // Mozilla 
      window.sidebar.addPanel( title, url, "" ); 
    } 
    catch (e) { 
      // Opera 
      if( typeof( opera ) == "object" ) { 
        a.rel = "sidebar"; 
        a.title = title; 
        a.url = url; 
        return true; 
      } 
      else { 
        // Unknown 
        alert( 'Press CTRL+D to add page to your bookmarks' ); 
      } 
    } 
  } 
  return false; 
}
</script>
<!-- bookmark end -->
<?php wp_footer(); ?>
<script type="text/javascript">
setTimeout(function(){var a=document.createElement("script");
var b=document.getElementsByTagName("script")[0];
a.src=document.location.protocol+"//dnn506yrbagrg.cloudfront.net/pages/scripts/0017/0693.js?"+Math.floor(new Date().getTime()/3600000);
a.async=true;a.type="text/javascript";b.parentNode.insertBefore(a,b)}, 1);
</script>
</body>
</html>