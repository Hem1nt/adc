<?php
class Manoj_Abandoned_Model_Mysql4_Abandonedorder extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("abandoned/abandonedorder", "abandoned_order_id");
    }
}