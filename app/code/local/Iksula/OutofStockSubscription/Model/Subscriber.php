<?php

class Iksula_OutofStockSubscription_Model_Subscriber extends Mage_Core_Model_Abstract
{
    /**
     * Init model
     */
    public function _construct()
    {
        $this->_init('outofstocksubscription/subscriber');
    }
}
