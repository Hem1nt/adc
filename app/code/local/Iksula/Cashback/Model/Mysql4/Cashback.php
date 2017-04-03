<?php
class Iksula_Cashback_Model_Mysql4_Cashback extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("cashback/cashback", "id");
    }
}