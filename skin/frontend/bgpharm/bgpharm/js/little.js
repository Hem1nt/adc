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
	
	function getCookie(name) {
		var dc = document.cookie;
		var prefix = name + "=";
		var begin = dc.indexOf("; " + prefix);
		if (begin == -1) {
			begin = dc.indexOf(prefix);
			if (begin != 0) return null;
		}
		else
		{
			begin += 2;
			var end = document.cookie.indexOf(";", begin);
			if (end == -1) {
				end = dc.length;
			}
		}
		return unescape(dc.substring(begin + prefix.length, end));
	} 

	var fontCookie = getCookie('fontclass');
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