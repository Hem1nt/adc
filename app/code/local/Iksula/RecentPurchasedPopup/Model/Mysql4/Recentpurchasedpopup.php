<?php
class Iksula_RecentPurchasedPopup_Model_Mysql4_Recentpurchasedpopup extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("recentpurchasedpopup/recentpurchasedpopup", "popup_id");
    }
}