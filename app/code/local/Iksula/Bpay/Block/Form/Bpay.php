<?php
class Iksula_Bpay_Block_Form_Bpay extends Mage_Payment_Block_Form_Ccsave
{
	protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('bpay/form.phtml');
    }
}
