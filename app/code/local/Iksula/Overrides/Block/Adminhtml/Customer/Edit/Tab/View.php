<?php

Class Iksula_Overrides_Block_Adminhtml_Customer_Edit_Tab_View extends Mage_Adminhtml_Block_Customer_Edit_Tab_View{
	public function getRegisterIp()
    {
        return $this->getCustomer()->getRegisterIp();
    }
    public function getContactNumber()
    {
        return $this->getCustomer()->getContactNumber();
    }
    public function getEmail()
    {
        return $this->getCustomer()->getEmail();
    }
    public function getCustomerBehavior()
    {
        $customer_id = $this->getCustomer()->getId();
        $_orders = Mage::getModel('sales/order')->getCollection()
                    ->addFieldToSelect('customer_behavior')
                    ->addFieldToFilter('customer_id',$customer_id);
        foreach ($_orders->getData() as $key) {
            foreach ($key as $k => $value) {
               if($value){
                /*Encode in saveCustomerBehaviorAction*/
                $return_behavior = json_decode($value,true);
                return $return_behavior['behavior_value'];
               }
            }
        }
    }
}