<?php
class Iksula_Variations_Model_Mysql4_Variations extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("variations/variations", "variations_id");
    }
}