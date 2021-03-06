<?php
class Iksula_Clicktoform_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function sendEmail($customerId){
		$model =  Mage::getModel('clicktoform/clicktoform')->load($customerId);
		$recepientEmail = $model->getCustomerEmail();
		$recepientName = $model->getCustomerName();
		$senderName = Mage::getStoreConfig('trans_email/ident_support/name');
		$senderEmail = Mage::getStoreConfig('clicktoform/clicktoform_setting/clicktoform_adminemailaddress');
		//$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
		$sender = array(
			'name' => $senderName,
			'email' => $senderEmail
		);
		$templateAdmin = Mage::getStoreConfig('clicktoform/clicktoform_setting/clicktoform_adminemailtemplate');
		$templateCustomer = Mage::getStoreConfig('clicktoform/clicktoform_setting/clicktoform_customeremail');
		// Get Store ID
		$storeId = Mage::app()->getStore()->getId();
		// Set variables that can be used in email template
		$vars = array('customername' => $model->getCustomerName(),
			'customermoblieno' =>$model->getCustomerMobileno(),
			'customertime' =>$model->getCustomerTime(),
			'customeremail'=>$model->getCustomerEmail());
		
		$translate  = Mage::getSingleton('core/translate');
		// Send Transactional Email
		/*Mage::getModel('core/email_template')->sendTransactional($templateAdmin, $sender, $senderEmail, 'Admin', $vars, $storeId);*/
		Mage::getModel('core/email_template')
		->sendTransactional($templateCustomer, $sender, $recepientEmail, $recepientName, $vars, $storeId);
		$translate->setTranslateInline(true);
		
	}
}