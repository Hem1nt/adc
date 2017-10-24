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
}