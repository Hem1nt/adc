
/**
*	Change dates
*
**/

function changeMonths(change){
	if(isNaN(change)) return;
	
	var el1 = document.getElementById(date_from);
	var el2 = document.getElementById(date_to);					
	if(!el1.value || !el2.value || change == 0){
		var date = new Date();
		el1.value = (new Date(date.getFullYear(), date.getMonth(), 1)).print(date_format);
		el2.value = (new Date(date.getFullYear(), date.getMonth(), 1)).print(date_format);
	}
	
	var firstDate = Date.parseDate(el1.value, date_format);
	var secondDate = Date.parseDate(el2.value, date_format);
	
	firstDate = new Date(firstDate.getFullYear(), firstDate.getMonth()+change, 1);
	secondDate = new Date(secondDate.getFullYear(), secondDate.getMonth()+1+change, 0);
	
	document.getElementById(date_from).value = firstDate.print(date_format);
	document.getElementById(date_to).value = secondDate.print(date_format);
}

function fromYearToToday(change){
	var date = new Date();
	var firstDate = new Date(date.getFullYear()+change, 0, 1);
	document.getElementById(date_from).value = firstDate.print(date_format);
	document.getElementById(date_to).value = date.print(date_format);
}


/**
*	Change multiselects
*
**/

function fireTrackChange(elemId, indexValue){
	document.getElementById(elemId).selectedIndex = indexValue;
	if ("createEvent" in document) {
		var evt = document.createEvent("HTMLEvents");
		evt.initEvent("change", false, true);
		document.getElementById(elemId).dispatchEvent(evt);
	} else document.getElementById(elemId).fireEvent("onchange");
}

function clearSelected(elemId, showElemId){
	fireTrackChange(showElemId, 0);
	var elem = document.getElementById(elemId);
	if(elem) elem.selectedIndex = -1;
}

function checkMultiselect(selectedVal, elemId, showElemId){
	fireTrackChange(showElemId, 1);
	
	var elem = document.getElementById(elemId);
	for(var i = 0; i < elem.length; i++) {
		elem.options[i].selected = false;
		for(var j = 0; j < selectedVal.length; j++){
			if(elem.options[i].value == selectedVal[j]) elem.options[i].selected = true;
		}
	}			

}

function checkOrderStatus(selectedStatuses){
	var selectedStatuses = Array.prototype.slice.call(arguments);
	if(selectedStatuses.length > 0) checkMultiselect(selectedStatuses, orderStatusElemId, orderStatusShowElemId);
	else clearSelected(orderStatusElemId, orderStatusShowElemId);
}


function checkPriceType(selectedPriceTypes){
	var selectedPriceTypes = Array.prototype.slice.call(arguments);
	if(selectedPriceTypes.length > 0) checkMultiselect(selectedPriceTypes, priceTypeElemId, priceTypeShowElemId);
	else clearSelected(priceTypeElemId, priceTypeShowElemId);
}

function checkCategory(selectedCategories){
	var selectedCategories = Array.prototype.slice.call(arguments);
	if(selectedCategories.length > 0) checkMultiselect(selectedCategories, categoryElemId, categoryShowElemId);
	else clearSelected(categoryElemId, categoryShowElemId);
}


/**
*	Change prices
*
**/
function changePriceRange(elName, val){
	if(isNaN(val)) val = '';
	document.getElementById(elName).value = val;
}

function changePriceFrom(from){
	changePriceRange(price_from, from);
}

function changePriceTo(to){
	changePriceRange(price_to, to);
}