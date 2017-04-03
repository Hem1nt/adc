<?php
class Iksula_Limitedsupply_Model_Mysql4_Limitedsupply extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("limitedsupply/limitedsupply", "id");
    }
}