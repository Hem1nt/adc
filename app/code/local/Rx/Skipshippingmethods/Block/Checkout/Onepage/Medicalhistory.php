<?php
class Rx_Skipshippingmethods_Block_Checkout_Onepage_Medicalhistory extends Mage_Checkout_Block_Onepage_Abstract{
	protected function _construct()
	{
		$this->getCheckout()->setStepData('medicalhistory', array(
            'label'     => Mage::helper('checkout')->__('Medical History'),
            'is_show'   => $this->isShow()
		));
		parent::_construct();
	}
}