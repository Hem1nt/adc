<?php

class TS_Reports_Model_Resource_Reportitem_Collectionmini extends Mage_Core_Model_Resource_Db_Collection_Abstract {
	
	protected function _construct(){
		$this->_init('ts_reports/reportitem');
	}
	
	public function getSelectCountSql(){
		if(count($this->getSelect()->getPart(Zend_Db_Select::GROUP)) > 0){
			$adapter = $this->getConnection();
		
			$cloneSelect = clone $this->getSelect();
			$cloneSelect->reset(Zend_Db_Select::ORDER);
			$cloneSelect->reset(Zend_Db_Select::LIMIT_COUNT);
			$cloneSelect->reset(Zend_Db_Select::LIMIT_OFFSET);			
			
			$countSelect = $adapter->select('*')->from(array('main' => $cloneSelect), array('COUNT(*)'));
			return $countSelect;
		} 
		return parent::getSelectCountSql();
    }

	protected function _initSelect(){
		parent::_initSelect();
		$columns = array(
			'order_item_id',
			'sku',
			'name',
			'price_type',
			'original_prices' => new Zend_Db_Expr("GROUP_CONCAT(DISTINCT original_price ORDER BY original_price ASC SEPARATOR '/')"),
			'order_date' 	 => new Zend_Db_Expr("GROUP_CONCAT(DISTINCT DATE(order_date) ORDER BY order_date ASC SEPARATOR '/')")
		);
		
		$this->getSelect()
			->reset(Zend_Db_Select::COLUMNS)->columns($columns)
			->joinRight( array('orders' => $this->getTable('sales/order')), 'main_table.order_id = orders.entity_id', array('base_currency_code') )
			->group('sku')->group('base_currency_code')->group('price_type');
        return $this;
    }
	
}