<?php
class Iksula_RecentPurchasedPopup_Model_Observer {
	
	public function getProductDetails(Varien_Event_Observer $observer) {
		//echo 'test';exit;
		$order = $observer->getEvent()->getOrder();
		foreach ($order->getAllItems() as $item) {
	        /*$items[] = array(
	            'id'            => $order->getIncrementId(),
	            'name'          => $item->getName(),
	            'sku'           => $item->getSku(),
	            'Price'         => $item->getPrice(),
	            'Ordered Qty'   => $item->getQtyOrdered(),


	        );*/
	        $productSku = $item->getSku();
	        if($productSku){
	        	$customerId = $order->getCustomer()->getData('entity_id');
	        	$shippingId = $order->getShippingAddress()->getId();
	        	break;
	        }
    	}

		if($productSku){
			$array = array('product_sku'=>$productSku,'customer_id'=>$customerId,'shipping_id'=>$shippingId);
			if(Mage::getModel('recentpurchasedpopup/recentpurchasedpopup')->load(1)){
				$popupData = Mage::getModel('recentpurchasedpopup/recentpurchasedpopup')->load(1);
				$popupData->setData($array);
				$popupData->save();
			}else{
				Mage::getModel('recentpurchasedpopup/recentpurchasedpopup')->setData($array)->save();
			}
			
		}
	}
	
}
