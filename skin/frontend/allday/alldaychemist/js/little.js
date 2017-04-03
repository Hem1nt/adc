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
jQuery(document).ready(function($){
	var fontCookie = get_cookie('fontclass');
	if(fontCookie) {
		jQuery('body').addClass(fontCookie);
	}
	$("#a1").click(function(){
		jQuery('body').removeClass('body-16');
		jQuery('body').removeClass('body-18');
		var now = new Date();
			var time = now.getTime();
			time += 3600 * 1000 * 700;
			now.setTime(time);
			document.cookie = 
			'fontclass=' + 
			'; expires=' + now.toGMTString() + 
			'; path=/';
		event.preventDefault();
	});

	$("#a2").click(function(){
		jQuery('body').removeClass('body-18');
		jQuery('body').addClass('body-16');
		var now = new Date();
			var time = now.getTime();
			time += 3600 * 1000 * 700;
			now.setTime(time);
			document.cookie = 
			'fontclass=body-16' + 
			'; expires=' + now.toGMTString() + 
			'; path=/';
		event.preventDefault();
	});
	
	$("#a3").click(function(){
		jQuery('body').removeClass('body-16');
		jQuery('body').addClass('body-18');
		var now = new Date();
			var time = now.getTime();
			time += 3600 * 1000 * 700;
			now.setTime(time);
			document.cookie = 
			'fontclass=body-18' + 
			'; expires=' + now.toGMTString() + 
			'; path=/';
		event.preventDefault();
	});
});