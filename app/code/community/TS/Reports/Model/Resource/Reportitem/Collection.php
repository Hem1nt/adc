<?php

class TS_Reports_Model_Resource_Reportitem_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {

	protected function _construct(){
		$this->_init('ts_reports/reportitem');
	}
	
	protected function _initSelect(){
		parent::_initSelect();
		$this->getSelect()
            ->joinRight( array('orders' => $this->getTable('sales/order')), 'main_table.order_id = orders.entity_id', array('entity_id', 'increment_id', 'base_currency_code') )
            ->joinRight( array('order_items' => $this->getTable('sales/order_item')), 'main_table.order_item_id = order_items.item_id', array('item_id') );
		return $this;
	}
}
