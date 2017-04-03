<?php
class Iksula_Otherpayments_Block_Form_Otherpayments extends Mage_Payment_Block_Form
{
	  protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('payment/form/otherpayments.phtml');
    }
}