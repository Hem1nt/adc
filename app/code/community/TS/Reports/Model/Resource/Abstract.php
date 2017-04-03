<?php

abstract class TS_Reports_Model_Resource_Abstract extends Mage_Core_Model_Resource_Db_Abstract {

	public function getOffset(){
		$timezone = new DateTimeZone( Mage::helper('ts_reports')->getMagentoTimezone() );
		return $timezone->getOffset( new DateTime("now", $timezone) );
	}
	
	public function getOffsetQuery($adapter, $column, $offset){
        return $adapter->getDateAddSql($column, $offset, Varien_Db_Adapter_Interface::INTERVAL_SECOND);
	}
	
}