<?php
class Iksula_Refillreminder_Model_Cron{	

	public function SendRefillReminder(){
		//do something
		      $template_Id = Mage::getStoreConfig('hide_attribute_frontend/custom_templateid/ext_template');
          //var_dump($template_Id);
          echo  $template_Id;
		      //$templateId = 124; 
          $model = Mage::getModel('refillreminder/refillreminder');

          $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
          $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');  
          $sender = array('name' => $senderName,
                'email' => $senderEmail);
          $order = Mage::getModel('sales/order')->loadByIncrementId($order_id); 
          $shippingAddress = $order->getShippingAddress();
          $customeremail=$shippingAddress->getEmail();
          
          //store email Id
          $storeemail=Mage::getStoreConfig('trans_email/ident_general/email');
          // Get Store ID   
           $store = Mage::app()->getStore()->getId();

           $reciever= array($customeremail, $storeemail);
           // Set variables that can be used in email template
            $vars = array('customer_name' => $name,
              'order_id' => $order_id);
            $translate  = Mage::getSingleton('core/translate');
      
            Mage::getModel('core/email_template')
              ->sendTransactional($templateId,$sender,$reciever,$vars, $storeId);
                    
            $translate->setTranslateInline(true); 
	} 
}