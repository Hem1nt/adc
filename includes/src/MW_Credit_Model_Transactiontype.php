<?php

class MW_Credit_Model_Transactiontype extends Varien_Object
{
    const ADMIN_CHANGE 				= 1;
    const REFUND_PRODUCT 			= 2;
    const CANCEL_WITHDRAWN			= 3;	
	const USE_TO_CHECKOUT			= 5;
	const CANCEL_USE_TO_CHECKOUT	= 6;
	const BUY_PRODUCT				= 7;
	const WITHDRAWN			    	= 8;
	const REFUND_PRODUCT_AFFILIATE  = 9;
		
    static public function getOptionArray()
    {
        return array(
			self::ADMIN_CHANGE   			=> Mage::helper('credit')->__('Changed By Admin'),	
    		self::USE_TO_CHECKOUT			=> Mage::helper('credit')->__('Use Credit to Checkout'),
    		self::CANCEL_USE_TO_CHECKOUT	=> Mage::helper('credit')->__('Canceled Order using Credit'),
    		self::REFUND_PRODUCT    		=> Mage::helper('credit')->__('Refund Product'),//khi tra lai don hang,ðõn hàng ðó dùng credit checkout
    		self::WITHDRAWN				    => Mage::helper('credit')->__('Withdraw'),
    		self::CANCEL_WITHDRAWN		    => Mage::helper('credit')->__('Canceled Withdraw'),
    		self::BUY_PRODUCT				=> Mage::helper('credit')->__('Invited Customer Buys Product'),
    		self::REFUND_PRODUCT_AFFILIATE  => Mage::helper('credit')->__('Invited Customer Refunds Product'),
        );
    }
    
    static public function getLabel($type)
    {
    	$options = self::getOptionArray();
    	return $options[$type];
    }
    
	static public function getTransactionDetail($type, $detail, $is_admin=false)
    {
    	$result = "";
    	if ($is_admin) $url = "adminhtml/sales_order/view";
    	else  $url = "sales/order/view";
    	
    	switch($type)
    	{
    		case self::ADMIN_CHANGE:
    			//$result = self::getLabel($type);
    			$result = Mage::helper('credit')->__($detail);
    			break;
    		case self::REFUND_PRODUCT:
    			$order = Mage::getModel("sales/order")->loadByIncrementId($detail);
    			$result = Mage::helper('credit')->__("You refurn to order : <b><a href=\"%s\">#%s</a></b>, checkout by credit",
    											  	  Mage::getUrl($url,array('order_id'=>$order->getId())),$detail
    												);
    			break;
    		case self::USE_TO_CHECKOUT:
    			$order = Mage::getModel("sales/order")->loadByIncrementId($detail);
    			$result = Mage::helper('credit')->__("You used credit to checkout order : <b><a href=\"%s\">#%s</a></b>",
    												  Mage::getUrl($url,array('order_id'=>$order->getId())),$detail
    												);
    			break;
    		case self::CANCEL_USE_TO_CHECKOUT:
    			$order = Mage::getModel("sales/order")->loadByIncrementId($detail);
    			$result = Mage::helper('credit')->__("You canceled order : <b><a href=\"%s\">#%s</a></b>, checkout by credit",
    												  Mage::getUrl($url,array('order_id'=>$order->getId())),$detail
    												);
    			break;
    		
    		case self::BUY_PRODUCT:
    			$order = Mage::getModel("sales/order")->loadByIncrementId($detail);
    			if ($is_admin)
    				$result = Mage::helper('credit')->__("Commission of order : <b><a href=\"%s\">#%s</a></b>",
    												  Mage::getUrl($url,array('order_id'=>$order->getId())),$detail
    												);
    			else
    				$result = Mage::helper('credit')->__("Commission of order: <b>#%s</b>",$detail);
    			break;
    		case self::REFUND_PRODUCT_AFFILIATE:
    			$order = Mage::getModel("sales/order")->loadByIncrementId($detail);
    			if ($is_admin)
    				$result = Mage::helper('credit')->__("Commission refund of order : <b><a href=\"%s\">#%s</a></b>",
    												  Mage::getUrl($url,array('order_id'=>$order->getId())),$detail
    												);
    			else
    				$result = Mage::helper('credit')->__("Commission refund of order: <b>#%s</b>",$detail);
    			break;
    		case self::WITHDRAWN:
    			$withdrawn = Mage::getModel("affiliate/affiliatewithdrawn")->load($detail);
    			$result = Mage::helper('credit')->__("Withdrawal money: <b>%s</b> (Taken fee: <b>%s</b>)",Mage::helper('core')->currency($withdrawn->getWithdrawnAmount()),Mage::helper('core')->currency($withdrawn->getFee()));
    			break;
    		case self::CANCEL_WITHDRAWN:
    			$withdrawn = Mage::getModel("affiliate/affiliatewithdrawn")->load($detail);
    			$result = Mage::helper('credit')->__("Canceled withdrawal money: <b>%s</b> (Taken fee: <b>%s</b>)",Mage::helper('core')->currency($withdrawn->getWithdrawnAmount()),Mage::helper('core')->currency($withdrawn->getFee()));
    			break;
    	   
    	}
    	return $result;
    }
}