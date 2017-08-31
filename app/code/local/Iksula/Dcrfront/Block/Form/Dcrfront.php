<?php
class Iksula_Dcrfront_Block_Form_Dcrfront extends Mage_Payment_Block_Form_Ccsave
{
	protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('dcrfront/form.phtml');
    }
}
