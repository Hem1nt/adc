<?php
class Iksula_Orderlog_Model_Mysql4_Ordertracking extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("orderlog/ordertracking", "id");
    }
}