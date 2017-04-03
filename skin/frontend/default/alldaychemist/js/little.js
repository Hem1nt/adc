<!-- HitTail Code -->
(function(){ var ht = document.createElement('script');ht.async = true;
ht.type='text/javascript';ht.src = '//107070.hittail.com/mlt.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ht, s);})();


jQuery(document).ready(function(){
	jQuery(".target_blank").click(function(event)
	{
		window.open(jQuery(this).attr('href'), "_blank", '' );
		event.preventDefault();
		return false;
	});
});