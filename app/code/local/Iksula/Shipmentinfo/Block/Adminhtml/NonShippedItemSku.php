<?php

Class Iksula_Shipmentinfo_Block_Adminhtml_NonShippedItemSku extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{

	public function render(Varien_Object $row)
		{
			$value =  $row->getEntityId();
			$order = Mage::getModel('sales/order')->load($value);
			foreach ($order->getAllVisibleItems() as $item){
				    if($item->getQtyOrdered() != $item->getQtyShipped()){
				    	$nonShippedSku[] = $item->getSku(); 
				    }
			}
			$skus = implode(',',$nonShippedSku);
			 
			if(empty($skus)){
				return '<span><b>All Items Shipped</b></span>';	
			}

			return 	$skus;
		}
}