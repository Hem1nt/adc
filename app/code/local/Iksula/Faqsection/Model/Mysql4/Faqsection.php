<?php
class Iksula_Faqsection_Model_Mysql4_Faqsection extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("faqsection/faqsection", "sections_typeid");
    }
}