<?php
class MW_Affiliate_Model_Affiliateinvitation extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('affiliate/affiliateinvitation');
    }
    
}