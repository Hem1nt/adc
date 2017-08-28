<?php
class Iksula_Dcrfront_Model_Observer
{
    public function __construct()
    {
    }
    public function sendEmailDcrFront(Varien_Event_Observer $observer)
    {
    	$order = $observer->getEvent()->getOrder();
        $payment = $order->getPayment()->getMethodInstance()->getCode();
        if($payment == 'virtual_dcrfront')
        {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $customerPlaced = $customer->getEmail();
            $customerFirstname = $customer->getFirstname();
            /*Email Function S*/
                $templateId = Mage::getStoreConfig('payment/dcrfront/email_template');
                // Set sender information            
                $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
                $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');        
                $sender = array('name' => $senderName,'email' => $senderEmail);    
                // Set recepient information
                $recepientEmail = $customerPlaced;
                $recepientName = $customerFirstname;  
                // Get Store ID        
                $storeId = Mage::app()->getStore()->getId();
                // Set variables that can be used in email template
                $vars = array('customer_name' => $customerFirstname);
                $translate  = Mage::getSingleton('core/translate');
                // Send Transactional Email
                Mage::getModel('core/email_template')
                ->addBcc($senderEmail)
                ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
                $translate->setTranslateInline(true);    
            /*Email Function E*/

        }
    }
    
}