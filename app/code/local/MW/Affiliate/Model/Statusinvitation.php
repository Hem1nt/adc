<?php

class MW_Affiliate_Model_Statusinvitation extends Varien_Object
{
	const INVITATION				= 1;		//haven't change points yet
    const CLICKLINK					= 2;
    const REGISTER			    	= 3;
    const PURCHASE			    	= 4;

    static public function getOptionArray()
    {
        return array(
            self::INVITATION    				=> Mage::helper('affiliate')->__('Invitation'),
            self::CLICKLINK  			 		=> Mage::helper('affiliate')->__('Click on referral link'),
            self::REGISTER	    		    	=> Mage::helper('affiliate')->__('Register account'),
            self::PURCHASE	    		    	=> Mage::helper('affiliate')->__('Purchase Product')
        );
    }
 	static public function getLabel($status)
    {
    	$options = self::getOptionArray();
    	return $options[$status];
    }
}