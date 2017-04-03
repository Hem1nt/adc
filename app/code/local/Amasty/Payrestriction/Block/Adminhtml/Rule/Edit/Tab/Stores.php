<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */ 
class Amasty_Payrestriction_Block_Adminhtml_Rule_Edit_Tab_Stores extends Amasty_Commonrules_Block_Adminhtml_Rule_Edit_Tab_Stores
{
    protected function _prepareForm()
    {
        $model = Mage::registry('ampayrestriction_rule');
        $this->_setRule($model);
        return parent::_prepareForm();
    }
}