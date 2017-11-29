<?php
class Iksula_Autodiscount_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isCustomerDiscountEnable(){
        $autoCustomerPromo = Mage::getStoreConfig('auto_customer_discount/auto_customer_discount_label/auto_customer_discount_id'); 
        $autoCustomerPromoCoupon = Mage::getModel('salesrule/rule')->load($autoCustomerPromo);
        $autoCustomerActive = $autoCustomerPromoCoupon->getData('is_active'); 
        return $autoCustomerActive;
    }
}
	 