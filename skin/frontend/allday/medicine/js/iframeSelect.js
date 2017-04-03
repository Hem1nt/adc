function equivalent() {
		var url = document.getElementById("equivalentVal").value;
    	var dropdown1Val = document.getElementById("dropdown1");
		var val = dropdown1Val.options[dropdown1Val.selectedIndex].value;
		window.top.location.href = url+"catalogsearch/result/?q="+val; 
} 

function generic() {
    	var dropdown2Val = document.getElementById("dropdown2");
		var val = dropdown2Val.options[dropdown2Val.selectedIndex].value;
	 	var url = document.getElementById("equivalentVal").value;
		window.top.location.href = url+"catalogsearch/result/?q="+val; 
}   
