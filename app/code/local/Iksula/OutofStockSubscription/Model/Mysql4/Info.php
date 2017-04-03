<?php

class Iksula_OutofStockSubscription_Model_Mysql4_Info extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
    {
         $this->_init('outofstocksubscription/info' , 'id');
    }

}
