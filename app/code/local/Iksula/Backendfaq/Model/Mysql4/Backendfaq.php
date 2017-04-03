<?php
class Iksula_Backendfaq_Model_Mysql4_Backendfaq extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("backendfaq/backendfaq", "id");
    }
}