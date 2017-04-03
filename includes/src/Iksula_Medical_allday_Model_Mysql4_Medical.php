<?php
class Iksula_Medical_Model_Mysql4_Medical extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("medical/medical", "id");
    }
}