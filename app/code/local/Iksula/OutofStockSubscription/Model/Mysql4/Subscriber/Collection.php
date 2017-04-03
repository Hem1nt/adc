<?php

/**
 * @category   BusinessKing
 * @package    BusinessKing_OutofStockSubscription
 */
class Iksula_OutofStockSubscription_Model_Mysql4_Subscriber_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Initialize collection
     *
     */
    public function _construct()
    {
        $this->_init('outofstocksubscription/subscriber');
    }

}
