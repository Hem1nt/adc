<?php
class Iksula_Bpay_Model_Observer
{
    public function __construct()
    {
    }
    public function sendEmailDummypayment(Varien_Event_Observer $observer)
    {
    	$order = $observer->getEvent()->getOrder();
        $payment = $order->getPayment()->getMethodInstance()->getCode();
        if($payment == 'dummypayment')
        {   
            $frontendHelper = Mage::helper('frontend');
            $encodedurl = $frontendHelper->encrypt_decrypt('encrypt',$order->getData('entity_id'));
            $paymentLinkUrl = Mage::getStoreConfig('custom_snippet/snippet/paymentlink');
            $payNowLink = $paymentLinkUrl.'?order_id='.$encodedurl.'&utm_source=back-payment&utm_medium=email&utm_campaign=pay-link';
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $customerPlaced = $customer->getEmail();
            $customerFirstname = $customer->getFirstname();
            /*Email Function S*/
                $templateId = Mage::getStoreConfig('payment/dummypayment/email_template');
                // Set sender information
                if($templateId){
                $senderName = Mage::getStoreConfig('trans_email/ident_sales/name');
                $senderEmail = Mage::getStoreConfig('trans_email/ident_sales/email');        
                $sender = array('name' => $senderName,'email' => $senderEmail);    
                // Set recepient information
                $recepientEmail = $customerPlaced;
                $recepientName = $customerFirstname;  
                // Get Store ID        
                $storeId = Mage::app()->getStore()->getId();
                // Set variables that can be used in email template
                $vars = array('pay_link'=>$payNowLink,'customer_name' => $customerFirstname);
                $translate  = Mage::getSingleton('core/translate');
                // Send Transactional Email
                Mage::getModel('core/email_template')
                ->addBcc($senderEmail)
                ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
                $translate->setTranslateInline(true); 
                }   
            /*Email Function E*/

        }
    }
    
}