<?php

class Iksula_Customerdelete_Model_Observer
{
	public function checkmoemailsend(Varien_Event_Observer $observer){

		$event = $observer->getEvent();
		$order = $event->getOrder(); 
		$payment_title = $order->getPayment()->getMethodInstance()->getCode();
		/* used to set custom order if customer_order_increment_id is not set*/
		if(!$order->getData('customer_order_increment_id')){
			$created_at = $order->getData('created_at');
			$ext = Mage::getStoreConfig('custom_snippet/custom_orderid/ext_name');
			$order_format = Mage::getModel('core/date')->date('dm-yhis',strtotime($created_at));
			$custom_order_id = $ext.''.$order_format.''.$order->getId();
			$order->setData('customer_order_increment_id',$custom_order_id)->save();
		}

		if($payment_title=='checkmo'){
			$templateId = 31;
			$from_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email 
			$from_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name
			$storeId = Mage::app()->getStore()->getId(); 
			$sender = array('name'  => $from_name, 'email' => $from_email);
			$recepientEmail = $order->getBillingAddress()->getEmail();
			$name = ucwords(strtolower($order->getBillingAddress()->getName()));
			$orderid = $order->getIncrementId();
			$customerOrderIncrementId = $order->getCustomerOrderIncrementId();
			$shippingregion = $order->getShippingAddress()->getCountry();
			$billingregion = $order->getBillingAddress()->getCountry();			

			if($billingregion == 'US'){
			    $sendername = Mage::getStoreConfig("custom_snippet/snippet/company_us_name");	
			    $address = "<div style='margin-left: 57px;line-height: 5px;'>".Mage::getStoreConfig("custom_snippet/snippet/derricwood_us_address")."</div>";	
			}else{
			    $sendername = Mage::getStoreConfig("custom_snippet/snippet/company_name");
			    $address = "<div style='margin-left: 57px;line-height: 5px;'>".Mage::getStoreConfig("custom_snippet/snippet/derricwood_address")."</div>";			
			}
			$recepientEmail = $order->getData("customer_email");
			$vars = array('address' => $address,
				'cust_name' => $name,'orderid'=> $orderid,'sender'=>$sendername,'customer_order_increment_id'=>$customerOrderIncrementId);
			Mage::log($vars,null,'var.log');
			try 
			{
					$mailTemplate = Mage::getModel('core/email_template')->load($templateId);
					$translate  = Mage::getSingleton('core/translate');
					$mailTemplate->sendTransactional($templateId, $sender, $recepientEmail, $recepientEmail,$vars, $storeId);

					if (!$mailTemplate->getSentSuccess()) {
						throw new Exception(); 
					} 
					$translate->setTranslateInline(true);
			} 
			catch (Exception $e) 
			{
					// echo $e->getMessage();
			}
		}

		/* for down payment */
		// if($payment_title=='pay'){
		// 	$templateId = 36;
		// 	$from_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email 
		// 	$from_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name
		// 	$storeId = Mage::app()->getStore()->getId(); 
		// 	$sender = array('name'  => $from_name, 'email' => $from_email);
			
		// 	$name = ucwords(strtolower($order->getShippingAddress()->getName()));
		// 	$orderid = $order->getIncrementId();
		// 	$customer =array();
		// 	$customer['cust_name'] =  $order->getBillingAddress()->getName();
		// 	$customer['order_id']= $order->getData('increment_id');
		// 	$customer['order_entity_id']= $order->getId();
		// 	$customer['amount']= sprintf("%0.2f", $order->getGrandTotal());
		// 	$customer['staus_label'] = $val;
		// 	$customer['sender'] = 'DERRIC WOOD';
		// 	$customer['customer_email'] = $order->getData("customer_email");
		// 	$customer['email'] = $order->getData("email");
		// 	$customer['street'] = $order->getShippingAddress()->getData('street');
		// 	$customer['region'] = $order->getShippingAddress()->getData('region');
		// 	$customer['city'] = $order->getShippingAddress()->getData('city');
		// 	$customer['postcode'] = $order->getShippingAddress()->getData('postcode');
		// 	$country_code = $order->getShippingAddress()->getData('country_id');
		// 	$billing_country_code = $order->getBillingAddress()->getData('country_id');
		// 	$customer['country'] = Mage::app()->getLocale()->getCountryTranslation($country_code);
		// 	$customer['bi_country'] = Mage::app()->getLocale()->getCountryTranslation($billing_country_code);
		// 	$paymentLinkUrl = Mage::getStoreConfig('custom_snippet/snippet/paymentlink');
		// 	$customer['link'] = $paymentLinkUrl.'/?order_id='.base64_encode($customer['order_entity_id']);
		// 	$shippingregion = $order->getShippingAddress()->getCountry();
		// 	$billingregion = $order->getBillingAddress()->getCountry();
			
		// 	$vars = $customer; //array('address' => $address,'name' => $name,'order_id'=> $orderid,'sender'=>$sendername);
		// 	$recepientEmail = $customer['customer_email'];
		// 	try 
		// 	{
		// 			$mailTemplate = Mage::getModel('core/email_template')->load($templateId);
		// 			$translate  = Mage::getSingleton('core/translate');
		// 			$mailTemplate->sendTransactional($templateId, $sender, $recepientEmail, $recepientEmail,$vars, $storeId);

		// 			if (!$mailTemplate->getSentSuccess()) {
		// 				//Mage::log('Mail sent', null, developer.log);
		// 				throw new Exception(); 
		// 			} 
		// 			$translate->setTranslateInline(true);
		// 	} 
		// 	catch (Exception $e) 
		// 	{
		// 		echo $e->getMessage();				
		// 	}
		// }
		
	}
// public function echeckids(Varien_Event_Observer $observer){
// 	echo '<pre>';
// 	$incrementId = 'ADC100021303';
// 	$increment_idArray=array('ADC100021303','ADC100021286','ADC100021280','ADC100021275','ADC100021270','ADC100021269','ADC100021263','ADC100021252','ADC100021240','ADC100021223','ADC100021220','ADC100021204','ADC100021203','ADC100021200','ADC100021173','ADC100021168','ADC100021167','ADC100021148','ADC100021020','ADC100021125','ADC100021120','ADC100021110','ADC100021093','ADC100020940','ADC100020937','ADC100020927'
// 		);
// 	$counter = 0;
// 	$echeck_transactionidArray = array('77992','77988','77983','77976','77974','77973','77969','77963','77955','77944','77936','77925','77924','77921','77887','77883','77881','77855','77841','77811','77809','77801','77790','77658','77655','77644');
// 	foreach ($increment_idArray as $value) {
// 		$order = Mage::getModel('sales/order')->load($value, 'increment_id');
// 		$id = $order->getId();
// 		$ordernew = Mage::getModel('sales/order')->load($id);
// 		$ordernew->setData('echeck_transactionid',$echeck_transactionidArray[$counter]);
// 		$ordernew->save();
// 		print_r($ordernew->getData());
// 		$counter++;
// 	}
// 	//exit;
// }



}