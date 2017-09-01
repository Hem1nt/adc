<?php
class Iksula_Echecksteps_Model_Mysql4_Echecksteps extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("echecksteps/echecksteps", "id");
    }
}