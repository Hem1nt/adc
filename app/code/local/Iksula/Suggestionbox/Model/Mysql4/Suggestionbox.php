<?php
class Iksula_Suggestionbox_Model_Mysql4_Suggestionbox extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("suggestionbox/suggestionbox", "sbox_id");
    }
}