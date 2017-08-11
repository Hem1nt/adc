<?php
class Iksula_Drc_Block_Form_Drc extends Mage_Payment_Block_Form_Ccsave
{
	protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('drc/form.phtml');
    }
}
