<?php
class Excellence_Ajaxcoupon_Model_Observer
{

    public function disableactivepaymentMethod(Varien_Event_Observer $observer) {

        $cartemail = $this->blackListEmail($cart);
        $event = $observer->getEvent();
        $method = $event->getMethodInstance();
        $result = $event->getResult();
        if($cartemail == 1 || Mage::getSingleton('core/session')->getBillingAddressSuspicious() == true || Mage::getSingleton('core/session')->getShippingAddressSuspicious() == true || Mage::getSingleton('core/session')->getPhoneSuspicious() == true){
                if($method->getCode() != 'dummypayment'){
                     $result->isAvailable = false;                          
                 }else{
               
                    $result->isAvailable = true;                          
             
                }       
            Mage::getSingleton('core/session')->setSuspicious(true);
        }else{
            if($method->getCode() == 'dummypayment'){
                $result->isAvailable = false;
            }
            Mage::getSingleton('core/session')->unsSuspicious();
            Mage::getSingleton('core/session')->unsBillingAddressSuspicious();
            Mage::getSingleton('core/session')->unsShippingAddressSuspicious();
            Mage::getSingleton('core/session')->unsPhoneSuspicious();
        }
    }

    public function checkSuspicious(Varien_Event_Observer $observer){

        if(Mage::getSingleton('core/session')->getSuspicious()){
            $event = $observer->getEvent();
            $order = $event->getOrder();
            $orderId = $order->getId();
            $orderObject = Mage::getModel('sales/order')->load($orderId);
            $jsonstring = array('suspicious_id' => 1 ,'suspicious_value' => 'Suspicious' );
            $orderObject->setData('suspicious',json_encode($jsonstring))->save();
            $cartemail = $this->blackListEmail();
            Mage::log('OrderId : '.$orderObject->getIncrementId().' , EmailId : '.Mage::getSingleton('core/session')->getSuspiciousEmail().' , BillingAddress : '.Mage::getSingleton('core/session')->getBillingAddressSuspicious().' , shippingAddress : '.Mage::getSingleton('core/session')->getShippingAddressSuspicious().' , phoneNumber : '.Mage::getSingleton('core/session')->getPhoneSuspicious(),true,'block_user.log');
            Mage::getSingleton('core/session')->unsSuspicious();
            Mage::getSingleton('core/session')->unsSuspiciousEmail();
        }else{
            Mage::getSingleton('core/session')->unsSuspicious();
            Mage::getSingleton('core/session')->unsSuspiciousEmail();
            Mage::getSingleton('core/session')->unsBillingAddressSuspicious();
            Mage::getSingleton('core/session')->unsShippingAddressSuspicious();
            Mage::getSingleton('core/session')->unsPhoneSuspicious();
        }

    }

    protected function blackListEmail($cart){
        if(Mage::getSingleton('customer/session')->isLoggedIn()){
            $cusemail = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
        }else{
            $cusemail = Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress()->getEmail();
        }
        foreach (unserialize(Mage::getStoreConfig("blacklist_section/blacklist/blacklist_email")) as $mapping) {
          if($mapping['email'] == $cusemail){
            Mage::getSingleton('core/session')->setSuspiciousEmail($cusemail);
            return 1;
            break;
          }
        }
    }

}
