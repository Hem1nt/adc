<?php
class Manoj_Abandoned_Model_Mysql4_Abandoned extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("abandoned/abandoned", "abandoned_cart_id");
    }
}