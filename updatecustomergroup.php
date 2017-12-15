<?php
ini_set('display_errors', 1);
require_once 'app/Mage.php';
Mage::app();
$orderCollection = Mage::getModel('sales/order')->getCollection();
$regularGroupId = 6;
$premiumGroupId = 2;
$newGroupId = 8;
$guestGroupId = 9;
$email_check = array();
foreach ($orderCollection as $key => $value) {
        $customerId = $value->getData('customer_id');
        $customerEmail = $value->getData('customer_email');
        if(in_array($customerEmail,$email_check)){
            continue;
        }

        if($customerEmail != ''){
            $email_check[] = $customerEmail;
            $collection = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_email',$customerEmail);
            $orderHelper = Mage::helper('frontend/order');
            $customersOrdersCount = $orderHelper->getCustomersOrdersCount($customerEmail);
            //$customersOrdersCount = count($collection->getData());
            //Mage::log($customerId.'----'.$customerEmail,true,'wholegroup.log');
            if($customerId){
                try{
                    if($customersOrdersCount >= 3){
                        foreach ($collection as $_order) {
                            $_order->setCustomerGroupId($premiumGroupId);
                            $_order->save();
                        }

                        $_customer = Mage::getModel('customer/customer')->load($customerId);
                        $_customer->setGroupId($premiumGroupId);
                        $_customer->save();

                    }elseif($customersOrdersCount >= 1 && $customersOrdersCount <= 2){
                        foreach ($collection as $_order) {
                            $_order->setCustomerGroupId($regularGroupId);
                            $_order->save();
                        }

                        $_customer = Mage::getModel('customer/customer')->load($customerId);
                        $_customer->setGroupId($regularGroupId);
                        $_customer->save(); 

                    }elseif($customersOrdersCount == 0){
                        foreach ($collection as $_order) {
                            $_order->setCustomerGroupId($newGroupId);
                            $_order->save();
                        }

                        $_customer = Mage::getModel('customer/customer')->load($customerId);
                        $_customer->setGroupId($newGroupId);
                        $_customer->save(); 
                    }
                }catch(Exception $e){
                    Mage::log($customerId.'----'.$customerEmail,true,'reggroup.log');
                }
            }else{
                try{
                    if($customersOrdersCount >= 3){
                        foreach ($collection as $_order) {
                            $_order->setCustomerGroupId($premiumGroupId);
                            $_order->save();
                        }
                    }elseif($customersOrdersCount >= 1 && $customersOrdersCount <= 2){
                        foreach ($collection as $_order) {
                            $_order->setCustomerGroupId($regularGroupId);
                            $_order->save();
                        }
                    }elseif($customersOrdersCount == 0){
                        foreach ($collection as $_order) {
                            $_order->setCustomerGroupId($guestGroupId);
                            $_order->save();
                        }
                    }
                }catch(Exception $e){
                    Mage::log($customerId.'----'.$customerEmail,true,'guestgroup.log');
            }
        }
    }    
}
//print_r($email_check);