jQuery(function($) {
    $('.lastviewed_data').each(function(){
        var widget_id = $(this).attr('id'),
            post_id = $(this).attr('data-post-id'),
            posts_per_widget = $(this).attr('data-post-per-widget'),
            newList = [],
            cookie = $.cookie(widget_id);

        if (cookie) {
            var oldList = ((cookie)).split(",");
            newList = $.grep(oldList, function(value) {
                return post_id != value;
            });
            newList.splice(parseInt(posts_per_widget-1)); //set eg. 39 values for max 40 with the push
        }
        newList.push(post_id);
        $.cookie(widget_id, newList, { expires:30, path:'/' });
    });
});