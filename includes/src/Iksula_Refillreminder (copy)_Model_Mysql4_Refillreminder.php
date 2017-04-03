<?php
class Iksula_Refillreminder_Model_Mysql4_Refillreminder extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("refillreminder/refillreminder", "reminder_id");
    }
}