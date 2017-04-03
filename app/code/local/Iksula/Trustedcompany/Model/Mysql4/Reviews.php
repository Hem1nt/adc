<?php
class Iksula_Trustedcompany_Model_Mysql4_Reviews extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("trustedcompany/reviews", "review_id");
    }
}