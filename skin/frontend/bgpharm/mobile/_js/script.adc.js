 jQuery(function($) {
    var $menu = $('nav#menu'),
        $html = $('html');

    $('#themes a').click(function() {
        $menu[ this.className == 'light' ? 'addClass' : 'removeClass' ]( 'mm-light' );
        $html.removeAttr( 'class' );
        $html.addClass( this.className == 'light' ? 'mm-light' : this.className );
    });

    $menu.mmenu();
});

jQuery(document).ready(function ($) {
    
    var heading = ".accordion a.accord";
    var pane = ".pane";
    $(heading).click(function(e){
        if(!$(this).hasClass('active') && $(this).next().hasClass('pane')) {
        	//alert("yes");
        	e.preventDefault();
            $(this).siblings(heading).removeClass('active');
            $(this).addClass('active');
            var parent = $(this).parent();
            $(pane, parent).slideUp(500);
            $(this).next().stop(true,true).delay(500).slideDown();
        }else if($(this).hasClass('active')){
        	e.preventDefault();
            $(this).removeClass('active');
            $(this).next().stop(true,true).slideUp(500);
        }
    });
});

// jQuery(document).ready(function ($) {
    
//     var heading = ".accordion a.accord";
//     var pane = ".pane";
//     $(heading).click(function(e){
//         if(!$(this).hasClass('active') && $(this).next().hasClass('pane')) {
//         	//alert("yes");
//         	e.preventDefault();
//             $(this).siblings(heading).removeClass('active');
//             $(this).addClass('active');
//             var parent = $(this).parent();
//             $(pane, parent).slideUp();
//             $(this).next().stop(true,true).slideDown();
//         }else if($(this).hasClass('active')){
//         	e.preventDefault();
//             $(this).removeClass('active');
//             $(this).next().stop(true,true).slideUp();
//         }
//     });
// });