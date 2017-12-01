jQuery(document).ready(function(){

	// jQuery('.checkout_steps div.tabs div.tab-link').click(function(){
	// 	var tab_id = jQuery(this).attr('data-tab');

	// 	jQuery('.checkout_steps div.tabs div.tab-link').removeClass('current');
	// 	jQuery('.tab-content').removeClass('current');

	// 	jQuery(this).addClass('current');
	// 	jQuery("#"+tab_id).addClass('current');

	// });

	//Check login status
	if(loginStatus == "1") {
		//alert(loginStatus);
		jQuery('.checkout_steps div.tabs div.tab-link').removeClass('current');
		jQuery('.tab-content').removeClass('current');
		jQuery('.checkoutstep-1').addClass('registred_user pass');
		jQuery('.checkoutstep-2').addClass('current');
		jQuery("#tab-2").addClass('current');
	}

	jQuery('input:radio[name="guest_checkout"]').change(function(){
	    if(jQuery(this).val() == 'registered') {
	       jQuery('li.liforpassword').show();
	       jQuery('#tab-1 .proceed-next').addClass("loader_login");
	    }
	    else {
	    	jQuery('li.liforpassword').hide();
	    	jQuery('#tab-1 .proceed-next').removeClass("loader_login");
	    }
	});

	// jQuery('#tab-1 .proceed-next.loader_login').live('click',function(){
	// 	IWD.OPC.Checkout.showLoader();
	// });

	jQuery('.checkoutstep-1').live('click',function() {		//for top tracker 1
		// alert(loginStatus);
		if(loginStatus != "1") {
			jQuery('#tab-2').hide();
			jQuery('#tab-3').hide();
			jQuery('#tab-4').hide();
			jQuery('#tab-1').show();
			jQuery('.checkoutstep-1').removeClass('pass');
			jQuery('.checkoutstep-1').addClass('current');
			jQuery(this).nextAll().removeClass('current');
			jQuery(this).nextAll().removeClass('pass');
		}
	});

	jQuery('.checkoutstep-2').live('click',function() {		//for top tracker 2
		// alert(loginStatus);
		if(jQuery('.checkoutstep-1').hasClass('pass')) {
			jQuery('#tab-1').hide();
			jQuery('#tab-3').hide();
			jQuery('#tab-4').hide();
			jQuery('#tab-5').hide();
			jQuery('#tab-2').show();
			jQuery('.checkoutstep-2').removeClass('pass');
			jQuery('.checkoutstep-2').addClass('current');
			jQuery(this).nextAll().removeClass('current');
			jQuery(this).nextAll().removeClass('pass');
		}
	});

	jQuery('.checkoutstep-3').live('click',function() {		//for top tracker 3
		// alert(loginStatus);
		if(jQuery('.checkoutstep-2').hasClass('pass')) {
			jQuery('#tab-1').hide();
			jQuery('#tab-2').hide();
			jQuery('#tab-4').hide();
			jQuery('#tab-5').hide();
			jQuery('#tab-3').show();
			jQuery('.checkoutstep-3').removeClass('pass');
			jQuery('.checkoutstep-3').addClass('current');
			jQuery(this).nextAll().removeClass('current');
			jQuery(this).nextAll().removeClass('pass');
		}
	});

	jQuery('.checkoutstep-4').live('click',function() {		//for top tracker 4
		// alert(loginStatus);
		if(jQuery('.checkoutstep-3').hasClass('pass')) {
			jQuery('#tab-1').hide();
			jQuery('#tab-2').hide();
			jQuery('#tab-3').hide();
			jQuery('#tab-5').hide();
			jQuery('#check_error_message').text('Please Confirm').hide();
			jQuery('#tab-4').show();
			jQuery('.checkoutstep-4').removeClass('pass');
			jQuery('.checkoutstep-4').addClass('current');
			jQuery(this).nextAll().removeClass('current');
			jQuery(this).nextAll().removeClass('pass');
		}
	});

	jQuery('#tab-1 .proceed-next').live('click',function(e) {			//1st step of checkout
		if(jQuery("input[name='guest_checkout']:checked").val()=='guest') {
			var theForm = new VarienForm('login-form');
		    if(!theForm.validator.validate()) { return false; }

			var emailid = jQuery('#email').val();
			var postUrl = IWD.OPC.Checkout.config.baseUrl+'onepage/index/verifyemail';
			// alert(postUrl);e.preventDefault();

			var ajaxcall = ajaxAction(emailid,postUrl);

			
		}
		else {
			var theForm = new VarienForm('login-form');
		    if(!theForm.validator.validate()) { return false; }

			jQuery('form#login-form').submit();
			jQuery('.checkoutstep-1').addClass('pass');
			jQuery('.checkoutstep-1').removeClass('current');
			jQuery('.checkoutstep-2').addClass('current');
		}
	});

	jQuery('#tab-2 .proceed-next').live('click',function() {			//2nd step of checkout
		//2nd step of checkout
		var status = true;
  		var theForm = new VarienForm('opc-address-form-billing');
	    if(!theForm.validator.validate()) {
	    	status = false;
	    }
		if(!jQuery('#billing\\:use_for_shipping_yes').is(":checked")) {
			var theForm = new VarienForm('opc-address-form-shipping');
		    if(!theForm.validator.validate()) {
		    	status = false;
		    }
		}
		if(!status){
			return;
		}
	  	var mode = IWD.OPC.Billing.need_reload_shippings_payment;
		IWD.OPC.Billing.need_reload_shippings_payment = false;
	    var valid = IWD.OPC.Billing.validateAddressForm();
	    if (valid){
		// start of ajax call for black list customers
			// jQuery.ajax({
			// 	url: IWD.OPC.Checkout.config.baseUrl+"onepage/json/checkblackListUser",
			// 	success : function (response)
			// 	{
			// 		ajaxResponse = response;
			// 		console.log(ajaxResponse);
			// 	}
			// // other properties
			// });
		// end of ajax call for black list customers

	    	proceedstatus = 1;
			IWD.OPC.Billing.saveShippingData();
			IWD.OPC.Billing.save();

			jQuery('#tab-2').hide();
			jQuery('#tab-3').show();
			jQuery('.checkoutstep-2').addClass('pass');
			jQuery('.checkoutstep-2').removeClass('current');
			jQuery('.checkoutstep-3').addClass('current');
		}
		else{
			if(mode != false)
				IWD.OPC.Checkout.checkRunReloadShippingsPayments(mode);
		}
		var option_val = jQuery("select#billing-address-select option:selected").val();
		if(option_val){
	    	proceedstatus = 1;
			IWD.OPC.Billing.saveShippingData();
			IWD.OPC.Billing.save();

			jQuery('#tab-2').hide();
			jQuery('#tab-3').show();
			jQuery('.checkoutstep-2').addClass('pass');
			jQuery('.checkoutstep-2').removeClass('current');
			jQuery('.checkoutstep-3').addClass('current');
		}
		else{
			if(mode != false)
				IWD.OPC.Checkout.checkRunReloadShippingsPayments(mode);
		}
	    
	});

	jQuery('#tab-3 .proceed-next').live('click',function() {			//3rd step of checkout
		var medicalobservationformForm = new VarienForm('medicalobservationform', true);

		if(medicalobservationformForm.validator &&  medicalobservationformForm.validator.validate()) {

			// start of ajax call for black list customers
			jQuery.ajax({
				url: IWD.OPC.Checkout.config.baseUrl+"onepage/json/blackListUser",
				success : function (response)
				{
					console.log(jQuery.parseJSON(response.status));
					ajaxResponse = jQuery.parseJSON(response);
					// alert(ajaxResponse);
					if(ajaxResponse.status == "YES") {
						// alert('here');
						window.location.href = IWD.OPC.Checkout.config.baseUrl+"checkout/cart/";
					}
				}
			// other properties
			});
		    // end of ajax call for black list customers

			savemedicalhistory();
        	jQuery('#tab-3').hide();
			jQuery('#tab-4').show();
			jQuery('.checkoutstep-3').addClass('pass');
			jQuery('.checkoutstep-3').removeClass('current');
			jQuery('.checkoutstep-4').addClass('current');
    	}
    	else {
    		return false;
    	}
	});

	//custom_address_step
	jQuery('#tab-4 .proceed-next').live('click',function() {			//4th step of checkout
        	jQuery('#tab-4').hide();
			jQuery('#tab-5').show();
			jQuery('.checkoutstep-4').addClass('pass');
			jQuery('.checkoutstep-4').removeClass('current');
			jQuery('.checkoutstep-5').addClass('current');
	});
	//custom_address_step

	jQuery('#forgotpassword-button-set .back-to-login').click(function(e){
        e.preventDefault();
        jQuery('#form-validate-email').hide();jQuery('#forgotpassword-button-set').hide();
        jQuery('#tab-1 .proceed-next').show();
        jQuery('#login-form').fadeIn();jQuery('#login-button-set').show();
    });
    /*
	jQuery("input[name='billing[postcode]']").blur(function(){
			var pincode = jQuery(this).val();
			jQuery.ajax({
				type: "POST",
				url: IWD.OPC.Checkout.config.baseUrl+"onepage/json/zipcode", //file which read zip code excel file
				dataType: "json",
				data: {'pincode' : pincode},
				beforeSend: function(){
					if(typeof IWD !== 'undefined'){
						// IWD.OPC.Checkout.showLoader();
					}
				},
				success: function(data){
					try{
						console.log(data);
						jQuery("input[name='billing[city]']").val(data.city);//city-class of city text filed
						if("select[name='billing[region_id]']"){
							jQuery("select[name='billing[region_id]'] option").each(function(){
        						if(jQuery(this).attr('title') == data.state){
            						jQuery(this).prop('selected', true);
        						}
								else{
									jQuery("input[name='billing[region]']").val(data.state); //region-class of state text field
								}
    						});
						}
						else{
							// alert("input");
							jQuery("input[name='billing[region]']").val(data.state); //region-class of state text field
						}
						if(typeof IWD !== 'undefined'){
							//IWD.OPC.Checkout.hideLoader();
						}
					}catch(e){
						alert(e.Message);
						// IWD.OPC.Checkout.hideLoader();
					}
				}
			});
		});
		
		jQuery("input[name='shipping[postcode]']").blur(function(){
			var pincode = jQuery(this).val();
			jQuery.ajax({
				type: "POST",
				url: IWD.OPC.Checkout.config.baseUrl+"onepage/json/zipcode", //file which read zip code excel file
				dataType: "json",
				data: {'pincode' : pincode},
				beforeSend: function(){
					if(typeof IWD !== 'undefined'){
						// IWD.OPC.Checkout.showLoader();
					}
				},
				success: function(data){
					try{
						jQuery("input[name='shipping[city]']").val(data.city);//city-class of city text filed
						if("select[name='shipping[region_id]']"){
							jQuery("select[name='shipping[region_id]'] option").each(function(){
        						if(jQuery(this).attr('title') == data.state){
            						jQuery(this).prop('selected', true);
        						}
								else{
									jQuery("input[name='shipping[region]']").val(data.state); //region-class of state text field
								}
    						});
						}
						else{
							// alert("input");
							jQuery("input[name='shipping[region]']").val(data.state); //region-class of state text field
						}
						// if(typeof IWD !== 'undefined'){IWD.OPC.Checkout.hideLoader();}
					}catch(e){
						alert(e.Message);
						// IWD.OPC.Checkout.hideLoader();
					}
				}
			});
		});

		jQuery("select[name='billing[country_id]").on('change', function() {
			jQuery("input[name='billing[city]']").val('');
			jQuery("input[name='billing[region]']").val('');
		});
		jQuery("select[name='shipping[country_id]").on('change', function() {
			jQuery("input[name='shipping[city]']").val('');
			jQuery("input[name='shipping[region]']").val('');
		});

	*/
});
function ajaxAction(emailid,postUrl)
{
	jQuery.ajax({
		url: postUrl,
		type: "POST",
		dataType: "json",
		data:  {email:emailid},
		success: function(data)
		{
			if(data == 0 || data == null) {
				jQuery('#billing\\:email').val(jQuery('div.input-box #email').val());
				jQuery('#tab-1').hide();
				jQuery('#tab-2').show();
				jQuery('.checkoutstep-1').addClass('pass');
				jQuery('.checkoutstep-1').removeClass('current');
				jQuery('.checkoutstep-2').addClass('current');
			} else {
				alert('Email id already registered.');
				e.preventDefault();
			}
		},
		error: function(){}
	});
}