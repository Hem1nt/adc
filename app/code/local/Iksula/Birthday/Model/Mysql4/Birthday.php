<?php
class Iksula_Birthday_Model_Mysql4_Birthday extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("birthday/birthday", "birthday_id");
    }
}