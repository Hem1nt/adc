<?php

class Iksula_Overrides_Model_Deleteshipment extends Mage_Core_Model_Abstract{  
	
	public function deleteInvoice($orderId){

		try{
			$order = Mage::getModel('sales/order')->load($orderId);
			// print_r($order->getData());exit();
			

		}catch(Exception $e)
		{
			return false;
		}	
	}


	public function deleteShipment($orderId)
	{

		try{
			$order = Mage::getModel('sales/order')->load($orderId);
			$shipment = $order->getShipmentsCollection();
			$status= $order->getStatus();

			$resource = Mage::getSingleton('core/resource');
			$writeConnection = $resource->getConnection('core_write');
			$readConnection = $resource->getConnection('core_read');
			foreach($shipment as $sp)
			{ 
				$query="delete from sales_flat_shipment where increment_id =".$sp->getIncrementId();
				$writeConnection->query($query);

				$query="delete from sales_flat_shipment_comment  where parent_id =".$sp->getId();
				$writeConnection->query($query);


				$query="delete from sales_flat_shipment_grid where increment_id =".$sp->getIncrementId();
				$writeConnection->query($query);

				$query="delete from sales_flat_shipment_track  where parent_id =".$sp->getId();
				$writeConnection->query($query); 
				
			}

			$query="update sales_flat_order_item set qty_shipped=0  where  order_id =".$order->getId();
			$writeConnection->query($query); 

			$query="delete from sales_flat_order_status_history  where   entity_name ='shipment' and parent_id =".$order->getId();
			$writeConnection->query($query); 
			

			$query="select entity_id from sales_flat_order_status_history    where   entity_name ='invoice' and parent_id =".$order->getId();
			$entity_id = $readConnection->fetchOne($query); 

			if($entity_id>0){
				$order_status = Mage_Sales_Model_Order::STATE_PROCESSING;
			}else{
				$order_status = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
			} 

			$order->setState($order_status, true)->save(); 

			return true;

		}
		catch(Exception $e)
		{
			return false;
		}	

	}
}
?>