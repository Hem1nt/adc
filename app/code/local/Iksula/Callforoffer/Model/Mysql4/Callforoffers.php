<?php
class Iksula_Callforoffer_Model_Mysql4_Callforoffers extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("callforoffer/callforoffers", "id");
    }
}