<?php
class Iksula_Customerdelete_Model_Mysql4_Customerdelete extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("customerdelete/customerdelete", "id");
    }
}