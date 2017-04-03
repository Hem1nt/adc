<?php

class TS_Reports_Model_Init_Statistics extends Mage_Core_Model_Abstract {

	public function init(){
		$refreshDate = Mage::helper('ts_reports')->getRefreshDate();
		Mage::helper('ts_reports')->setRefreshDate( date('Y-m-d H:i:s', time()) );
		Mage::getResourceModel('ts_reports/orderitem')->init($refreshDate);
		Mage::getResourceModel('ts_reports/order_aggregated')->init($refreshDate);
	}
	
}