<?php   
class Iksula_ImportOrder_Block_Index extends Mage_Core_Block_Template{   

 public function oldorders() {
	  $customer = Mage::getSingleton('customer/session')->getCustomer();
	 $email = $customer->getEmail();// for email address	
	// var_dump($email);
	$model = Mage::getModel('importorder/getorder');
	
	$collection = $model->getCollection();
	$collection->addFieldToFilter('customer_email', $email);

	return $collection;

 
 }


	
 public function orderdetails($order_id) {
	
	 
	// var_dump($email);
	$model = Mage::getModel('importorder/getorderdetails');
	
	$details_collection = $model->getCollection();
	$details_collection->addFieldToFilter('order_id', $order_id);

	return $details_collection;

 
 }
 
 public function order_count() {
	  $customer = Mage::getSingleton('customer/session')->getCustomer();
	 $email = $customer->getEmail();// for email address	
	// var_dump($email);
	$model = Mage::getModel('importorder/getorder');
	
	$collection = $model->getCollection();
	$collection->addFieldToFilter('customer_email', $email);

	return $collection;

 
 }


}