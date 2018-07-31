<?php
class Iksula_Clicktoform_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function SendEmail($customerId){

 	   	$model =  Mage::getModel('clicktoform/clicktoform')->load($customerId);
 	   	$customerEmail = $model->getCustomerEmail();
 	   	$customerName = $model->getCustomerName();

 	   	
 	   
 	   	$templateAdmin = Mage::getStoreConfig('clicktoform/clicktoform_setting/clicktoform_settings');
 	   	$templateCustomer = Mage::getStoreConfig('clicktoform/clicktoform_setting/clicktoform_customeremail');
 	   	// Get Store ID   
        $storeId = Mage::app()->getStore()->getId();
 	   	
 	   	$vars = array('customername' => $model->getCustomerName(),
		  'customermoblieno' =>$model->getCustomerMoblieno(),
		  'customertime' =>$model->getCustomerTime());

 	   	$senderName = Mage::getStoreConfig('trans_email/ident_support/name');
		//$senderEmail = Mage::getStoreConfig('clicktoform/clicktoform_setting/clicktoform_adminemailaddress');
		$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');;
		$senderForAdmin = array('name' => $senderName,'email' => $senderEmail);
		$senderForCustomer = array('name' => $senderName,'email' => $senderEmail);
 	   	$transactionalEmail = Mage::getModel('core/email_template')->setDesignConfig(array('area'=>'frontend', 'store'=>1));

		$transactionalEmail->sendTransactional($templateAdmin, $senderForAdmin, $senderName, 'Admin', $vars, $storeId);

		$transactionalEmail->sendTransactional($templateCustomer, $senderForCustomer, $customerName, $recepientName, $vars, $storeId);
            
    }
}