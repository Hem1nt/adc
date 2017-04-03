<?php

/**
 * Abstract Rule entity data model
 *
 * @category Mage
 * @package Mage_Rule
 * @author Magento Core Team <core@magentocommerce.com>
 */
 
require_once 'Mage/SalesRule/Model/Rule.php';

class Addons_Skipshippingmethods_Model_Type_Abstract extends Mage_SalesRule_Model_Rule
{
    protected $_conditions;
    protected $_actions;
    protected $_form;
    protected $_isDeleteable = true;
    protected $_isReadonly = false;
  
	
    protected function _beforeSave()
    {
		
        // Check if discount amount not negative
        if ($this->hasDiscountAmount()) {
			if($this->getData('name')=="Extra Charge for order")
			{
			}else if ((int)$this->getDiscountAmount() < 0) {
                Mage::throwException(Mage::helper('rule')->__( 'Invalid Discount Amount'));
            }
        }

        // Serialize conditions
        if ($this->getConditions()) {
            $this->setConditionsSerialized(serialize($this->getConditions()->asArray()));
            $this->unsConditions();
        }

        // Serialize actions
        if ($this->getActions()) {
            $this->setActionsSerialized(serialize($this->getActions()->asArray()));
            $this->unsActions();
        }

        /**
         * Prepare website Ids if applicable and if they were set as string in comma separated format.
         * Backwards compatibility.
         */
        if ($this->hasWebsiteIds()) {
            $websiteIds = $this->getWebsiteIds();
            if (is_string($websiteIds) && !empty($websiteIds)) {
                $this->setWebsiteIds(explode(',', $websiteIds));
            }
        }

        /**
         * Prepare customer group Ids if applicable and if they were set as string in comma separated format.
         * Backwards compatibility.
         */
        if ($this->hasCustomerGroupIds()) {
            $groupIds = $this->getCustomerGroupIds();
            if (is_string($groupIds) && !empty($groupIds)) {
                $this->setCustomerGroupIds(explode(',', $groupIds));
            }
        }

        //parent::_beforeSave();
        return $this;
    }

    
}
