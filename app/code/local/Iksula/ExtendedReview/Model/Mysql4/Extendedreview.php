<?php
class Iksula_ExtendedReview_Model_Mysql4_Extendedreview extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("extendedreview/extendedreview", "id");
    }
}