<?php
class Iksula_ImportOrder_Model_Mysql4_Getorderdetails extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("importorder/getorderdetails", "id_order_detail");
    }
}