<?php

Class Iksula_Shipmentinfo_Block_Adminhtml_ShippedItem extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{

	public function render(Varien_Object $row)
		{
			$value =  $row->getEntityId();
			$order = Mage::getModel('sales/order')->load($value);
			$shippedItems = (int)0;

			foreach ($order->getAllVisibleItems() as $item){
			   $shippedItems += $item->getQtyShipped();  
			}

			if($shippedItems == 0){
				return '0';
			}
			return $shippedItems;
		}
}