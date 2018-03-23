<?php
class Excellence_Ajaxcoupon_Model_Observer
{

    public function disableactivepaymentMethod(Varien_Event_Observer $observer) {

        $cartemail = $this->blackListEmail($cart);
        if($cartemail == 1 || Mage::getSingleton('core/session')->getBillingAddressSuspicious() == true || Mage::getSingleton('core/session')->getShippingAddressSuspicious() == true || Mage::getSingleton('core/session')->getPhoneSuspicious() == true){
            /*$payments = Mage::getSingleton('payment/config')->getActiveMethods();
            foreach ($payments as $paymentCode=>$paymentModel) {
                $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
                $methods[] = $paymentCode;
            }*/
            $event = $observer->getEvent();
            $method = $event->getMethodInstance();
            $result = $event->getResult();
            //if (!empty($methods)) {
                //foreach ($methods as $key => $value) {
                     if($method->getCode() != 'dummypayment'){
                         $result->isAvailable = false;                          
                     }else{
                   
                        $result->isAvailable = true;                          
                 
                    }
               
                //}
            //}

            Mage::getSingleton('core/session')->setSuspicious(true);
        }else{
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
            Mage::getSingleton('core/session')->unsSuspicious();
        }else{
            Mage::getSingleton('core/session')->unsSuspicious();
            Mage::getSingleton('core/session')->unsBillingAddressSuspicious();
            Mage::getSingleton('core/session')->unsShippingAddressSuspicious();
            Mage::getSingleton('core/session')->unsPhoneSuspicious();
        }

    }

    protected function blackListEmail($cart){
        foreach (unserialize(Mage::getStoreConfig("blacklist_section/blacklist/blacklist_email")) as $mapping) {
          $cusemail = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
          if($mapping['email'] == $cusemail){
            return 1;
          }
        }
    }

}
