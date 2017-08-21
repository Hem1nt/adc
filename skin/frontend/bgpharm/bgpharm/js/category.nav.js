 //alert("FO");

 jQuery(document).ready(function($){
 	var get_category_column_count = $(".all_categories_list #nav ul").size();
 	var get_category_count = $(".all_categories_list #nav ul li").size();
 	var number_categories_exist_per_column = Math.floor(get_category_count / get_category_column_count);
 	var updated_category_count = Math.floor(get_category_count/4); // For resolution 1024px

 	if((get_category_count%4) >= 1){
 		updated_category_count = updated_category_count+1;
 	}

 	var substractive_number_of_category = updated_category_count - number_categories_exist_per_column;

 	var counter = substractive_number_of_category;
 	//alert(get_category_count +" number_categories_exist_per_column : "+ number_categories_exist_per_column + " , updated_category_count : " + updated_category_count + ", counter : " + counter );

 	$(".all_categories_list #nav > li").each(function(){
 		var get_index_of_category = $(this).next("li").find("ul > li");
 		$(get_index_of_category).each(function(){
 			if(counter > $(this).index()) {
	 			$(this).addClass("added");
	 			var clone_el = $(this).clone();
	 			$(this).addClass("existing");
	 			var get_closest_parent = $(this).closest("#nav > li");	 			
 				$(get_closest_parent).prev("li").find("ul").append(clone_el);
	 		}
 			//console.log($(this).index()+ $(this).text()); 
 			//$(this).prepend($(this).index());
 		});
 		counter = counter + substractive_number_of_category; 		
 	});


 });
