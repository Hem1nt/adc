<?php
// header('Content-type: application/json');
require_once('app/Mage.php');
Mage::app('default');

$order_entity_id = Mage::app()->getRequest()->getParam('orderid');
if($order_entity_id){
	$order = Mage::getModel('sales/order')->load($order_entity_id);
	$order_inc_id = $order->getIncrementId();
	$customer =array();
	$customer['order_details'] =  $order->getData(); 
	$bi_country_code = $order->getBillingAddress()->getData('country_id');
	$sp_country_code = $order->getShippingAddress()->getData('country_id');
	$customer['billing']['country'] = Mage::app()->getLocale()->getCountryTranslation($bi_country_code);
	$customer['shipping']['country'] = Mage::app()->getLocale()->getCountryTranslation($sp_country_code);
	foreach ($order->getBillingAddress()->getData() as $billaddressKey=>$billaddressValue) {
		$customer['billing'][$billaddressKey]= $billaddressValue;          
	}
	// foreach ($order->getShippingAddress()->getData() as $shipaddressKey=>$shipaddressValue) {
	// 	$customer['shipping'][$shipaddressKey]= $shipaddressValue;          
	// }
	// $customerDOB = Mage::getModel("customer/customer")->loadByEmail($customer['customer_email'])->getData('dob');
	// $customer['billing']['dob'] = $customerDOB;
	// $customer['shipping']['dob'] = $customerDOB;  
	echo json_encode($customer);	
}
