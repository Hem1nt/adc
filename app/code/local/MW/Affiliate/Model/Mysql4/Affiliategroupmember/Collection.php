<?php

class MW_Affiliate_Model_Mysql4_Affiliategroupmember_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('affiliate/affiliategroupmember');
    }
}