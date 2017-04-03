<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */ 
class Amasty_Payrestriction_Model_Rule extends Amasty_Commonrules_Model_Rule
{
    public function _construct()
    {
        $this->_type = 'ampayrestriction';
        parent::_construct();
    }
    
    public function restrict($method)
    {
        return (false !== strpos($this->getMethods(), ',' . $method->getCode() . ','));
    }
}