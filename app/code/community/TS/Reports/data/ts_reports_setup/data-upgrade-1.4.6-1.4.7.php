<?php

// gather different stores, calculate timezones for each of them. update!

$adapter = Mage::getSingleton('core/resource')->getConnection('core_write');
$stores = Mage::app()->getStores();

foreach($stores as $store){
	$storeId = $store->getId();
	$tz = Mage::helper('ts_reports')->getTimezoneOffset($storeId);
	$columns = array('order_item_id' => 'order_item_id', 'tz_name' => new Zend_Db_Expr($adapter->quote($tz['timezone'])), 'tz_offset' => new Zend_Db_Expr($tz['offset']));
	
	$select = $adapter->select()->from(array('ri' => $this->getTable('ts_reports/reportitem')), $columns)->where('store_id = ?', $storeId);
	
	$adapter->query($select->insertFromSelect($this->getTable('ts_reports/reportitem'), array_keys($columns), Varien_Db_Adapter_Interface::INSERT_ON_DUPLICATE));
	$adapter->commit();
}

Mage::getResourceModel('ts_reports/reportitem')->initRulePrices();