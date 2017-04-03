<?php

Class Iksula_Cashback_Model_Observer {
	public function getCashback(Varien_Event_Observer $observer){
		$order = $observer->getEvent()->getOrder();
		$customerEmail = $order->getCustomerEmail();
		$customerId = $order->getCustomerId();
		$customerIsGuest = $order->getCustomerIsGuest(); 
		$customerOrderCount = Mage::helper('cashback')->getCustomersOrdersCount($customerId);
		$moduleStatus = Mage::helper('cashback')->isEnable();
		$orderCouponCode = $order->getCouponCode();
		$orderStatus = $order->getStatus();
		$orderId = $order->getId();
		$statusList = Mage::getStoreConfig('order_status/general/selected_status');
		$statusArray = explode(',',$statusList);
		$useMonth = date('MY');
		$currentTime = date('Y-m-d');
		$activationDate = date('Y-m-d' , strtotime('next month'));
		$expireDate = date('Y-m-d', strtotime('+ 1 year',strtotime($activationDate)));
		if(in_array($orderStatus,$statusArray)){
			if($moduleStatus){
				$couponCollectionData = Mage::getModel('cashback/cashback')->getCollection()->addFieldToFilter('rule_order_id',$orderId);
				$couponCollectionDataCount = $couponCollectionData->count();
				if($couponCollectionDataCount <= 0){
					if (strpos($orderCouponCode,'CB-') !== false) {
						$collectionData = Mage::getModel('cashback/cashback')->getCollection()->addFieldToFilter('coupon_code',$orderCouponCode);
						$collectionData->getFirstItem()->setUses('1')->save();
						$collectionData->getFirstItem()->setOrderId($orderId)->save();
						$collectionData->getFirstItem()->setUseMonth($useMonth)->save();
					}
					if($customerIsGuest != 1 && $customerOrderCount > 1){
						$cashbackCollection = Mage::getModel('cashback/cashback')->getCollection()
						                      ->addFieldToFilter('user_email',$customerEmail)
						                      ->addFieldToFilter('uses',0);
						$cashbackCount = $cashbackCollection->count();
						if($cashbackCount == 0){
							$sub = $order->getSubtotal();
							$dis = $order->getDiscountAmount();
							$orderSubtotal = ($sub+$dis);
							$maxDiscountInPercentage = Mage::helper('cashback')->maxDiscountInPercentage();
							$discountAmount = ($orderSubtotal/100)*$maxDiscountInPercentage;
							$discountAmount = round($discountAmount,2);
							$maxDiscountInValue = Mage::helper('cashback')->maxDiscountInValue();
							if($discountAmount < $maxDiscountInValue){
								$finalDiscountAmount = $discountAmount;
							}else{
								$finalDiscountAmount = $maxDiscountInValue;
							}
							$noOfCoupon = Mage::helper('cashback')->noOfCoupon();
							$ruleAmount = round(($finalDiscountAmount/$noOfCoupon),2);
							for($i=0;$i<$noOfCoupon;$i++){
								$couponCode = Mage::helper('cashback')->getCouponCode($ruleAmount);
								$dataArr = array('user_id'=>$customerId,
									             'user_email'=>$customerEmail,
									             "coupon_code"=>$couponCode,
									             'amount'=>$ruleAmount,
									             'created_at'=>$currentTime,
									             'coupon_active_date'=>$activationDate,
									             'coupon_expire_date'=>$expireDate,
									             'use_month'=>'',
									             'rule_order_id'=>$orderId,
									             'order_id'=>'',
									             'uses'=>0,
									             );
								Mage::getModel('cashback/cashback')->setData($dataArr)->save();
							}
						}                   
					}
				}
			}
		}else{
			if($moduleStatus){
				$couponCollectionData = Mage::getModel('cashback/cashback')->getCollection()->addFieldToFilter('rule_order_id',$orderId);
				$couponCollectionDataCount = $couponCollectionData->count();
				if($couponCollectionDataCount){
					foreach ($couponCollectionData as $key) {
						$key->delete()->save();
					}
				}
			}
		}
	}

	public function removeCashbackCoupon($observer){
		$moduleStatus = Mage::helper('cashback')->isEnable();
		if($moduleStatus){
			$minimumCartValue = Mage::helper('cashback')->minimumCartValue();
			$quote = Mage::getModel('checkout/session')->getQuote();
			$collection = Mage::getBlockSingleton('checkout/cart_totals');
        	$total = $collection->getTotals();
        	$subtotal = $total['subtotal']['value'];	
			$orderCouponCode = $quote->getCouponCode();
			if (strpos($orderCouponCode,'CB-') !== false) {
				if($subtotal < $minimumCartValue){
					$quote->setCouponCode('')->save()->collectTotals()->save();
				}
			}
		}
	}
}