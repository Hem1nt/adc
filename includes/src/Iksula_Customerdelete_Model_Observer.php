<?php

class Iksula_Customerdelete_Model_Observer
{
	public function checkmoemailsend(Varien_Event_Observer $observer){
		Mage::log('customer_check_money');
		$event = $observer->getEvent();
		$order = $event->getOrder(); 
		$payment_title = $order->getPayment()->getMethodInstance()->getCode();
		if($payment_title=='checkmo'){
			$templateId = 31;
			$from_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email 
			$from_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name
			$storeId = Mage::app()->getStore()->getId(); 
			$sender = array('name'  => $from_name, 'email' => $from_email);
			$recepientEmail = $order->getBillingAddress()->getEmail();
			$name = ucwords(strtolower($order->getShippingAddress()->getName()));
			$orderid = $order->getIncrementId();
			// exit;
			//$lastname = $order->getShippingAddress()->getLastname();
			$shippingregion = $order->getShippingAddress()->getCountry();
			$billingregion = $order->getBillingAddress()->getCountry();
			// $recepientName ='Nilesh Yadav';
			$address ="
			M/S DERRIC WOOD<br/>
			UNIT NO. 702, 703, 704 GOPAL HEIGHTS<br/>
			PLOT NO D-9, NETAJI SUBHASH PLACE<br/>
			PITAMPURA DELHI-110034<br/>
			INDIA<br/>";
			$sendername = 'DERRIC WOOD';
			if($billingregion=='US'){
				$sendername ='INTERNET MERCHANT TRANSACTIONS';
				$address ="INTERNET MERCHANT TRANSACTIONS<br/>
				2764 N GREEN VALLEY PKWY STE 537<br/>
				HENDERSON NEVADA 89014<br/>
				USA<br/>";}
			$vars = array('address' => $address,
				'cust_name' => $name,'orderid'=> $orderid,'sender'=>$sendername);
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
					echo $e->getMessage();
			}
		}
		
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