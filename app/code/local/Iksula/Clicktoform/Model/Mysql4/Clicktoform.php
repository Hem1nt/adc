<?php
class Iksula_Clicktoform_Model_Mysql4_Clicktoform extends Mage_Core_Model_Mysql4_Abstract
{
	//protected $_isPkAutoIncrement    = false;
    protected function _construct()
    {
        $this->_init("clicktoform/clicktoform", "customeform_id");
    }
}