<?php
class Iksula_RecentPurchasedPopup_Model_Observer {
	
	public function getProductDetails(Varien_Event_Observer $observer) {
		//echo 'test';exit;
		$order = $observer->getEvent()->getOrder();
		foreach ($order->getAllItems() as $item) {
	        $productSku = $item->getSku();
	        if($productSku){
	        	$customerId = $order->getCustomer()->getData('entity_id');
	        	$shippingId = $order->getShippingAddress()->getId();
	        	$orderId = $order->getId();
	        	break;
	        }
    	}

		if($productSku){
			$array = array('product_sku'=>$productSku,'customer_id'=>$customerId,'shipping_id'=>$shippingId,'order_id'=>$orderId);
			if(Mage::getModel('recentpurchasedpopup/recentpurchasedpopup')->load(1)){
				$popupData = Mage::getModel('recentpurchasedpopup/recentpurchasedpopup')->load(1);
				$popupData->addData($array);
				$popupData->save();
			}else{
				Mage::getModel('recentpurchasedpopup/recentpurchasedpopup')->setData($array)->save();
			}
			
		}
	}
	
}
