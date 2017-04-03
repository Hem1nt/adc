<?php

Class Iksula_Shipmentinfo_Block_Adminhtml_ShippedItemSku extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{

	public function render(Varien_Object $row)
		{
			$value =  $row->getEntityId();
			$order = Mage::getModel('sales/order')->load($value);
			foreach ($order->getAllVisibleItems() as $item){
				    if($item->getQtyOrdered() == $item->getQtyShipped()){
				    	$shippedSku[] = $item->getSku(); 
				    }
			}	
			$skus = implode(',',$shippedSku);
			 
			if(empty($skus)){
				return '<span><b>Not a single item shipped yet</b></span>';	
			}

			return 	$skus;
		 
		}
}