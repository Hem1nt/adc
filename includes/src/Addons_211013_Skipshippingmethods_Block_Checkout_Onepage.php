<?php
class Addons_Skipshippingmethods_Block_Checkout_Onepage extends Mage_Checkout_Block_Onepage
{
protected function _getStepCodes()
{
    
	//return array('login', 'billing', 'shipping', 'shipping_method', 'payment', 'review');
    return array('login', 'billing','shipping', 'payment', 'review');
}
}
?>