<?php
class Iksula_Refillreminder_Model_Cron{	

	public function SendRefillReminder(){
		//do something
    //current date calulcation
         $model = Mage::getModel('refillreminder/refillreminder');
          $collection = $model->getCollection();
          foreach($collection->getData() as $myAllReminders) 
          {
            $order=Mage::getModel('sales/order')->loadByIncrementId($order_id);
            $enityId=$order->getEntityId();

          
          $currentDate = Mage::getModel('core/date')->date('d');
          // $order_id=$myAllReminders['order_Id'];
         // $createDate=$myAllReminders['created_date'];
          //create date in days
            $currentDate = Mage::getModel('core/date')->date('d');
          // $order_id=$myAllReminders['order_Id'];
          $createDate=$myAllReminders['created_date'];
          $createDate = new DateTime($createDate);

          $strip = $createDate->format('d');
          

          //create date end in days
          $reminderDate=$myAllReminders['reminder_days'];
          if(($strip+$currentDate) % $reminderDate =='0')
          {
            $template_Id = Mage::getStoreConfig('hide_attribute_frontend/custom_templateid/ext_template');
          //var_dump($template_Id);
          echo  $template_Id;
          //$templateId = 124; 
          $customerSession = Mage::getSingleton('customer/session');
          if($customerSession->isLoggedIn()) {
          $email = $customerSession->getCustomer()->getEmail();
          $name = $customerSession->getCustomer()->getName();
            }
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
              'order_Id' => $order_id);
            $translate  = Mage::getSingleton('core/translate');
      
            Mage::getModel('core/email_template')
              ->sendTransactional($templateId,$sender,$reciever,$vars, $storeId);
                    
            $translate->setTranslateInline(true); 
          }
          else
          {
            echo "no email fired";
          }


          }

		      
	} 
}