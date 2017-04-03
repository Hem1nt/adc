<?php
class Iksula_Shipmentinfo_Model_Mysql4_Shipmentinfo extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("shipmentinfo/shipmentinfo", "entity_id");
    }
}