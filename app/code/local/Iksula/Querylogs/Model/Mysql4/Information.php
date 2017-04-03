<?php
class Iksula_Querylogs_Model_Mysql4_Information extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("querylogs/information", "contact_id");
    }
}