<?php
class Iksula_Dummypayment_Model_Observer
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
            //Mage::log($order,true,'block_user.log');
        }
    }
    
}