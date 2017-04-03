<?php
class Iksula_Faqsection_Model_Mysql4_Faqquestions extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("faqsection/faqquestions", "question_id");
    }
}