<?php
class Iksula_Prescription_Model_Mysql4_Prescription extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("prescription/prescription", "id");
    }
}