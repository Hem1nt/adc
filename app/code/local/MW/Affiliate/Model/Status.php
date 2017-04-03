<?php

class MW_Affiliate_Model_Status extends Varien_Object
{
	const PENDING				= 1;		//haven't change points yet
    const COMPLETE				= 2;
    const CANCELED			    = 3;
    const CLOSED				= 4;
    const INVOICED				= 5;
	

    static public function getOptionArray()
    {
        return array(
            self::PENDING    				=> Mage::helper('affiliate')->__('Pending'),
            //self::INVOICED  			 	=> Mage::helper('affiliate')->__('Invoiced'),
            self::CANCELED	    		    => Mage::helper('affiliate')->__('Canceled'),
            self::COMPLETE  			 	=> Mage::helper('affiliate')->__('Complete'),
            self::CLOSED  			 	    => Mage::helper('affiliate')->__('Closed'),
        );
    }
	static public function getOptionAction()
    {
        return array(
        	self::CANCELED	    		    => Mage::helper('affiliate')->__('Canceled'),
            self::COMPLETE  			 	=> Mage::helper('affiliate')->__('Complete'),
            self::CLOSED  			 	    => Mage::helper('affiliate')->__('Closed'),
        );
    }
 	static public function getLabel($status)
    {
    	$options = self::getOptionArray();
    	return $options[$status];
    }
}