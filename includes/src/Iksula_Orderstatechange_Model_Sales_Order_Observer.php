<?php
class Iksula_Orderstatechange_Model_Sales_Order_Observer extends Mage_Core_Model_Abstract
{
	public function productCollectionsLowStock($productid){
		$product = Mage::getModel('catalog/product')->load($productid);
	
	}
	
	public function getProductStock($productId){
	
		return (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId)->getQty();;
	
	}
	
	
	public function sales_order_place_before($observer){
	$stockStatusValue = array();
	$Order = $observer->getOrder();
	$OrderId = $Order->getIncrementId();
	$orderData = Mage::getModel('sales/order')->loadByIncrementId($OrderId);
	$item = $orderData->getItemsCollection()->getData();
	//print_r($item);exit;
	$isOutOfStock = false;
	foreach($item as $items){
	
		if($items['product_type'] == 'simple'){
			$qtyOrdered = (int)$items['qty_ordered'];
			$productId = $items['product_id'];
			$stockStatus = $this->getProductStock($productId);
			$stockStatusValue[] = $stockStatus + $qtyOrdered;
			$productQty = $stockStatus + $qtyOrdered;
			if( $productQty <= 0){
				
				$isOutOfStock=true;
			}
		
		}
	}
	//echo $productQty;exit;
	if($isOutOfStock){
	
		$orderData->setState(Iksula_Orderstatechange_Model_Sales_Order::STATE_PENDING_STOCK, true)->save();
	
	}
	
	//$orderData->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true)->save();
	}
	
	public function salesOrderInvoiceSaveAfter($observer){
	
	   $invoices=$observer->getInvoice()->getData();
	   
		if(!empty($invoices)){
			$orderData = Mage::getModel('sales/order')->load($invoices['order_id']);
			$status= $orderData->getStatus();
			
			Mage::getSingleton('core/session')->setStatusVal($status);
		}
        return $this;
    }
	
	public function salesOrderInvoiceSaveCommitAfter($observer){
	
	   $invoices=$observer->getInvoice()->getData();
	   
		if(!empty($invoices)){
		
			$orderData = Mage::getModel('sales/order')->load($invoices['order_id']);
			$status= $orderData->getStatus();
			
			$status= Mage::getSingleton('core/session')->getStatusVal();
			//echo "sdf--".$status;exit;
			$orderData->setStatus($status)->save();
			Mage::getSingleton('core/session')->unsStatusVal();
		
		 }
        
        return $this;
    }
	
	/*
	   author-Samir rath
	   calling the shipmentcommitsave observer to change the status to shipped
	*/
	public function salesOrderShipmentSaveAfter($observer){
	
	   if(Mage::getSingleton('core/session')->getDubleEvent() != "yes")
	 {
		   $shipment=$observer->getShipment()->getData();
		 
			if(!empty($shipment)){
			
				$orderData = Mage::getModel('sales/order')->load($shipment['order_id']);	
				//$status= Mage::getSingleton('core/session')->getStatusVal();
				$status= $orderData->getStatus();
				
				Mage::getSingleton('core/session')->setStatusVal($status);
			 }
			//Mage::unregister('link_invoice_entity_id');
			//Mage::register('link_invoice_entity_id', $invoice->getEntityId());

			return $this;
	 }
    }
	
	public function salesOrderShipmentSaveCommitAfter($observer){
	
       if(Mage::getSingleton('core/session')->getDubleEvent() != "yes")
	 {	
		   $shipment=$observer->getShipment()->getData();
		 
			if(!empty($shipment)){
			
				$orderData = Mage::getModel('sales/order')->load($shipment['order_id']);	
				$status= Mage::getSingleton('core/session')->getStatusVal();
				//echo "sdf--".$status;exit;
				$orderData->setStatus($status)->save();
				Mage::getSingleton('core/session')->unsStatusVal();
			 }
			//Mage::unregister('link_invoice_entity_id');
			//Mage::register('link_invoice_entity_id', $invoice->getEntityId());

			return $this;
	  }
	  else
	  {
		Mage::getSingleton('core/session')->unsDubleEvent();
	  }
    }
}
?>