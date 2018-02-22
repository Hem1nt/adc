jQuery(document).ready(function(){
	jQuery('.emailid_form_class').hide();
	jQuery('.change_email_id').click(function(){
		jQuery('.emailid_form_class').show();
		jQuery('.change_email_id').hide();
	});
});
 function supply_issue_comment(url){

	var supply_issue_data = jQuery('#supply_issue_data').val();
	var orderid = jQuery('#order_view_id').val();
	if(supply_issue_data){
		new Ajax.Request(url, {
			method: 'Post',
			parameters: {supply_issue_data:supply_issue_data,orderid:orderid},
			onComplete: function(transport) {

				alert('Data updated successfully');

			}
		});
	}else{
		alert('Please enter appropriate product name');
	}

 }

 function dispatcher_comment(url){

	var dispatcher_data = jQuery('#dispatcher_data').val();
	var orderid = jQuery('#des_order_view_id').val();
	if(dispatcher_data){
		new Ajax.Request(url, {
			method: 'Post',
			parameters: {dispatcher_data:dispatcher_data,orderid:orderid},
			onComplete: function(transport) {

				alert('Data updated successfully');

			}
		});
	}else{
		alert('Please enter appropriate descriptor name');
	}

 }

 function savecomment(url){

	var major_comment = jQuery('#major_comment').val();
	var orderid = jQuery('#des_order_view_id').val();
	if(major_comment){
		new Ajax.Request(url, {
			method: 'Post',
			parameters: {major_comment:major_comment,orderid:orderid},
			onComplete: function(transport) {

				alert('Data updated successfully');

			}
		});
	}else{
		alert('Please enter appropriate descriptor name');
	}

 }


 function fromdate_comment(url){
	var fromdate_data = jQuery('#from_date').val();
	var orderid = jQuery('#fdate_order_view_id').val();
	if(fromdate_data){
		new Ajax.Request(url, {
			method: 'Post',
			parameters: {fromdate_data:fromdate_data,orderid:orderid},
			onComplete: function(transport) {

				alert('Data updated successfully');

			}
		});
	}else{
		alert('Please enter appropriate from date');
	}

 }


 function todate_comment(url){

	var todate_data = jQuery('#to_date').val();
	var orderid = jQuery('#order_view_id').val();
	if(dispatcher_data){
		new Ajax.Request(url, {
			method: 'Post',
			parameters: {todate_data:todate_data,orderid:orderid},
			onComplete: function(transport) {

				alert('Data updated successfully');

			}
		});
	}else{
		alert('Please enter appropriate to date');
	}

 }

  function paymentinfo_comment(url){

	var paymentinfo_data = jQuery('#paymentinfo_data').val();
	var orderid = jQuery('#payment_order_view_id').val();

	if(paymentinfo_data){
		new Ajax.Request(url, {
			method: 'Post',
			parameters: {paymentinfo_data:paymentinfo_data,orderid:orderid},
			onComplete: function(transport) {

				alert('Data updated successfully');

			}
		});
	}else{
		alert('Please enter appropriate Payment Information');
	}

 } 

  // function hideEmailAddress(){
  // 	 jQuery('.emailid_form_class').hide();
  // 	 jQuery('.change_email_id').show();
  // }
  function changeEmailAddress(url){
  	var emailaddrsss = jQuery('.email_address').val();
    var orderid = jQuery('.orderid').val();

    console.log(emailaddrsss);
    console.log(orderid);
	if(emailaddrsss && orderid){
		new Ajax.Request(url, {
			method: 'Post',
			parameters: {isAjax: 1, method: 'POST',email:emailaddrsss,orderid:orderid},
			onComplete: function(transport) {
				jQuery('.custome_emailid').html(transport['responseText']);
				jQuery('.custom_emailid_href').attr('href','mailto:'+transport['responseText']);
				jQuery('.emailid_form_class').hide();
				jQuery('.change_email_id').show();
			}
		});
	}else{
		alert('Email Address can not be Empty');
	}

 }

 function saveCustomerBehavior(url,customerId){
    var orderid = jQuery('.orderid').val();
    var emailaddrsss = jQuery('.custome_emailid').text();
    var behavior_id = jQuery('input[name=behavior]:checked').val();
    var behavior_value = jQuery('input[name=behavior]:checked').parent('label').text();
	if(behavior_id && orderid && customerId){
		new Ajax.Request(url, {
			method: 'Post',
			parameters: {isAjax: 1, method: 'POST',behavior_id:behavior_id,behavior_value:behavior_value,orderid:orderid,customerId:customerId,email:emailaddrsss},
			onComplete: function(transport) {
				jQuery('#behavior_message').html('Behavior Updated');
			}
		});
	}else{
		alert('Please Select Behavior');
	}

 }



/************ kyc custom code start ***********/

  function savekyc(url,customerId){
    var orderid = jQuery('.orderid').val();
    var emailaddrsss = jQuery('.custome_emailid').text();
    var kyc_id = jQuery('input[name=kyc]:checked').val();

    var kyc_value = jQuery('input[name=kyc]:checked').parent('label').text();

	if(kyc_id && orderid && customerId){
		new Ajax.Request(url, {
			method: 'Post',
			parameters: {isAjax: 1, method: 'POST',kyc_id:kyc_id,kyc_value:kyc_value,orderid:orderid,customerId:customerId,email:emailaddrsss},
			onComplete: function(transport) {
				jQuery('#kyc_message').html('KYC Saved');
			}
		});
	}else{
		alert('Please Select KYC');
	}

 }


/************ kyc custom code end ***********/

// jQuery(document).ready(function(){
// 	var img_url = <?php echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'images/plus-sign1.png' ?>
// 	more_discount = jQuery(".data_2").prepend('<span class="more_offers"><img src="'+img_url+'" style="height:100%"/></span>');
// 	jQuery('.data_2').click(function(){
// 		jQuery('.discount_block').toggle();
// 	});
// });
