<?php
class Iksula_Autodiscount_Model_Observer
{
    public function autoCustomerDiscount($observer)
	{   
        $quote = $observer->getQuote();
        $quote = Mage::getSingleton('checkout/cart')->getQuote();
        if($quote->getAllVisibleItems())
        {   
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                $customer_email = $customer->getEmail();
                $customer = Mage::getModel('customer/customer');
                $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
                $customer->loadByEmail($customer_email);

                if($customer->getGroupId() == 1){
                    $autoCustomerPromo = Mage::getStoreConfig('auto_customer_discount/auto_customer_discount_label/auto_customer_discount_id');
                    $autoCustomerPromoCoupon = Mage::getModel('salesrule/rule')->load($autoCustomerPromo);
                    $autoCustomerActive = Mage::helper('autodiscount')->isCustomerDiscountEnable();
                    $autoCustomerDiscount = $autoCustomerPromoCoupon->getData('coupon_code');
                    $getCustomerAuto = Mage::getModel('core/cookie')->get('autoPromoApplied');
                    if($getCustomerAuto == 'referFriendWelcome10' && $autoCustomerActive == 1) {
                        Mage::getSingleton('checkout/session')->getMessages(true); // The true is for clearing them after loading them
                        Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode($autoCustomerDiscount);
                        Mage::getModel('core/cookie')->delete('autoPromoApplied');//once coupon is applied cookie is deleted
                    }
                }else{
                    Mage::getModel('core/cookie')->delete('autoPromoApplied');
                    return;
                }
            }else{
                Mage::getModel('core/cookie')->delete('autoPromoApplied');
                return;
            }
            
        }else{
            Mage::getModel('core/cookie')->delete('autoPromoApplied');
            return;
        }
    }
}
?>