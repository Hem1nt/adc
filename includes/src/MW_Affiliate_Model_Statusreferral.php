<?php

class MW_Affiliate_Model_Statusreferral extends Varien_Object
{
	const ENABLED				= 1;		//haven't change points yet
    const LOCKED				= 2;
  
    static public function getOptionArray()
    {
        return array(
            self::ENABLED    				=> Mage::helper('affiliate')->__('Enabled'),
            self::LOCKED  			 		=> Mage::helper('affiliate')->__('Locked'),
        );
    }
 	static public function getLabel($status)
    {
    	$options = self::getOptionArray();
    	return $options[$status];
    }
}