<?php

class Iksula_OutofStockSubscription_Model_Mysql4_Subscriber extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('outofstocksubscription/subscriber', 'id');
    }
}
