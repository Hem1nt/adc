<?php
class Iksula_ImportOrder_Model_Mysql4_Getorder extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("importorder/getorder", "old_order_id");
    }
}