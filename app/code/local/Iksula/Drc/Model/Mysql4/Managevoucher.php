<?php
class Iksula_Drc_Model_Mysql4_Managevoucher extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("drc/managevoucher", "id");
    }
}