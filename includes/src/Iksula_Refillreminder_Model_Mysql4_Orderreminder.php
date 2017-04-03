<?php
class Iksula_Refillreminder_Model_Mysql4_Orderreminder extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("refillreminder/orderreminder", "order_inc_id");
    }
}