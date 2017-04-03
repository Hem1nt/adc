<?php

class TS_Reports_Model_Resource_Invoiceitem_Collection extends Mage_Sales_Model_Resource_Order_Invoice_Item_Collection {

	public function loadByOrderItemId($orderItemId){
		return $this->addFieldToFilter('order_item_id',$orderItemId);
	}

}