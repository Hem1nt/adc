jQuery(document).ready(function($){
	$(".more_info_btn").click(function(){
		$(".desc_area").slideToggle();
	});


	/* Category page more details accordion */
	$(".example2 li").first().addClass("active");
	jQuery('.example2').accordion({
	    canToggle: true,
	    canOpenMultiple: false
	});
	jQuery(".loading").removeClass("loading");

	var filter_cat_value =$(".catalogsearch-result-index .block-layered-nav dt").first().text();
	if ( filter_cat_value == "Category"){
		$(".catalogsearch-result-index .block-layered-nav dt").first().css("display","none");
	}


});
	function hoverfunction(class_name,data_row,data_index) {


        step_id = data_index;
        step_datarow = data_row;
        console.log(step_id);
        console.log(step_datarow);
        jQuery('.box'+step_datarow).hide();
        jQuery('.outer_box'+step_datarow+step_id).show();


    // if (jQuery(".processed-current")[0]){

    //     jQuery(".status_step.processed-current").trigger("mouseover");

    // } else {

    // }

	}


