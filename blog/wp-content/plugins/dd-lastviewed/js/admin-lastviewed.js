( function( $ ) {

    var bind_ids = [];

    $( document ).ajaxComplete(function() {
        $('.js-types-and-terms').select2();
    });

    $( document ).on( "click",".widget[id*='dd_last_viewed-']", function() {
        var $this = $(this),
            thisID = $this.attr('id');

        if($.inArray(thisID, bind_ids) == -1) {
            $this.find('.js-types-and-terms').select2();
            bind_ids.push(thisID);
        }

    });

    
    $(document).on('click','.dd-switch', function(){
        $(this).toggleClass('on');
        var value = $(this).is('.on') ? true : false;
        $(this).next('input').attr('checked', value);

        var this_lv_link = $(this).next().next('.lv_link');

        var lv_link_val = this_lv_link.is('.button-primary'); //? true : false;

        if(!value && lv_link_val ){
            this_lv_link.trigger("click");

        }

    });

    $(document).on('click','.lv_link', function(){
        $(this).toggleClass('button-primary');
        var value = $(this).is('.button-primary') ? true : false;
        $(this).next('input').attr('checked', value);
    });
    
    $(document).on('click','.js-collapse', function(e){
        e.preventDefault();
        
        $(this).next().toggleClass('visible');
    });

} )( jQuery );