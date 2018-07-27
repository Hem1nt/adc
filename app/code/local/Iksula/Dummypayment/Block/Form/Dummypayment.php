<?php
class Iksula_Dummypayment_Block_Form_Dummypayment extends Mage_Payment_Block_Form_Ccsave
{
	protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('dummypayment/form.phtml');
    }
}
