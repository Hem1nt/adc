<?php

class TS_Reports_Model_System_Config_Source_Select_Orderstatus {

	private function reformatOrderStatuses($entry){
		if($entry['state'] == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT || $entry['state'] == Mage_Sales_Model_Order::STATE_NEW) return null;
		$entry['value'] = $entry['status'];
		unset($entry['status']);
		return $entry;
	}

    public function toOptionArray(){
        $orderStatuses = Mage::getModel('sales/order_status')->getCollection()->joinStates()->getData();
		$orderStatuses = array_filter(array_map(array($this,'reformatOrderStatuses'), $orderStatuses));
		return $orderStatuses;
    }
	
}