<?php

Class Iksula_Shipmentinfo_Block_Adminhtml_NonShippedItem extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{

	public function render(Varien_Object $row)
		{
			$value =  $row->getEntityId();
			$order = Mage::getModel('sales/order')->load($value);
			$qtyOrder = $order->getTotalQtyOrdered();
			$shippedItems = (int)0;

			foreach ($order->getAllVisibleItems() as $item){
			   $shippedItems += $item->getQtyShipped();  
			}

			$nonShippedItems = $qtyOrder - $shippedItems;
			if($nonShippedItems == 0){
				return '0';
			}
			return $nonShippedItems;
		}
}