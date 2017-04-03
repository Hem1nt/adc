<?php
class Iksula_Cashback_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isEnable(){
		return Mage::getStoreConfig('cashback/general/status');
	}

	public function maxDiscountInPercentage(){
		return Mage::getStoreConfig('cashback/general/max_discount_percentage');
	}

	public function maxDiscountInValue(){
		return Mage::getStoreConfig('cashback/general/max_discount_value');
	}

	public function noOfCoupon(){
		return Mage::getStoreConfig('cashback/general/no_of_coupon');
	}

	public function minimumCartValue(){
		return Mage::getStoreConfig('cashback/general/minimum_cart_total');
	}

	public function getCustomersOrdersCount($customerId){
		$status = Mage::getStoreConfig('order_status/general/selected_status');
		$statusArray = explode(',',$status);
		$_orders = Mage::getModel('sales/order')->getCollection()
				  ->addFieldToFilter('status',array('in' => $statusArray))
				  ->addFieldToFilter('customer_id',$customerId);
		$_orderCount = $_orders->getSize(); 
		return $_orderCount;

	}

	public function getCouponCode($amount){
		$customerGroupIds = Mage::getModel('customer/group')->getCollection()->getAllIds();
		$couponCode = $this->getRandomCode();
		$ShoppingCartPriceRules = Mage::getModel('salesrule/rule'); 
		$ShoppingCartPriceRules->setName('Cashback');
		$ShoppingCartPriceRules->setDescription('Cashback');
		$ShoppingCartPriceRules->setFromDate(''); 
		$ShoppingCartPriceRules->setCouponType(2);
		$ShoppingCartPriceRules->setCouponCode($couponCode); 
		$ShoppingCartPriceRules->setUsesPerCoupon(1);
		$ShoppingCartPriceRules->setUsesPerCustomer(1);
		$ShoppingCartPriceRules->setCustomerGroupIds($customerGroupIds);
		$ShoppingCartPriceRules->setIsActive(1);
		$ShoppingCartPriceRules->setStopRulesProcessing(0);
		$ShoppingCartPriceRules->setIsRss(0);
		$ShoppingCartPriceRules->setIsAdvanced(1);
		$ShoppingCartPriceRules->setProductIds('');
		$ShoppingCartPriceRules->setSortOrder(0);
		$ShoppingCartPriceRules->setSimpleAction('cart_fixed');
		$ShoppingCartPriceRules->setDiscountAmount($amount);
		$ShoppingCartPriceRules->setDiscountQty(0);
		$ShoppingCartPriceRules->setDiscountStep(0);
		$ShoppingCartPriceRules->setSimpleFreeShipping(0);
		$ShoppingCartPriceRules->setApplyToShipping(0);
		$ShoppingCartPriceRules->setWebsiteIds('1'); 

		$ShoppingCartPriceRules->loadPost($ShoppingCartPriceRules->getData());
		$ShoppingCartPriceRules->save();
		return $couponCode;
	}

	public function getRandomCode(){
		$string = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$string = str_shuffle($string);
		$finalString = substr($string,0,10);
		$couponCode = 'CB-'.$finalString;
		$cashbackModel = Mage::getModel('cashback/cashback')->getCollection()->addFieldToFilter("coupon_code",$couponCode);
		$couponCodeCount = $cashbackModel->count();
		if($couponCodeCount){
			$this->getRandomCode();
		}else{
			return $couponCode;
		}
	}

	public function getCouponCollection($uses){
		$moduleStatus = Mage::helper('cashback')->isEnable();
		$curDate = date('Y-m-d');
		if($moduleStatus){
			$userEmail = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
			$cashbackCollection = Mage::getModel('cashback/cashback')->getCollection()
			                      ->addFieldToFilter('user_email',$userEmail)
			                      ->addFieldToFilter('coupon_active_date',array('lteq'=>$curDate));
			if($uses){
				$cashbackCollection->addFieldToFilter('uses','0');
			}                      
            return $cashbackCollection; 			                      
		}
	}
}
	 