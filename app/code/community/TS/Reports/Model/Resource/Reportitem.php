<?php

class TS_Reports_Model_Resource_Reportitem extends Mage_Core_Model_Resource_Db_Abstract {

	public function _construct(){
		$this->_init('ts_reports/reportitem', 'order_item_id');
	}
	
	public function init($orderId = null, $storeId = null){
		$adapter = $this->_getWriteAdapter();
		
		try {			
			$updatedAt = date('Y-m-d H:i:s', time());
			
			$columns = array(
				'order_item_id'		=> 'oi.item_id',
				'order_date'		=> 'o.created_at',
				'store_id'			=> 'o.store_id',
				'order_id'			=> 'o.entity_id',
				'customer_group_id'	=> 'o.customer_group_id',
				'sku'				=> 'oi.sku',
				'product_id'		=> 'oi.product_id',
				'name'				=> 'oi.name',
				'categories'		=> 'c.categories',
				'original_price'	=> 'oi.original_price',
				'base_price'		=> 'oi.base_price',
				'price_incl_tax'	=> 'oi.price_incl_tax',
				'price_type'		=> new Zend_Db_Expr(
					sprintf('IF(oi.base_price = q.custom_price, %s,
								IF(oi.original_price = p.value, %s, 
									IF(oi.original_price = sp.value AND (spf.value IS NULL OR o.created_at >= spf.value) AND (spt.value IS NULL OR o.created_at <= spt.value), %s,
										IF(oi.original_price = gr.value, %s, 
											IF(oi.original_price = ti.value, %s, %s)
										)
									)
								)
							)', TS_Reports_Model_Types::ADMIN,
								TS_Reports_Model_Types::REGULAR,
								TS_Reports_Model_Types::SPECIAL, 
								TS_Reports_Model_Types::GROUP, 
								TS_Reports_Model_Types::TIER,  
								TS_Reports_Model_Types::UNKNOWN
					)
				),
				'cart'				=> new Zend_Db_Expr('IF(abs(oi.discount_amount) > 0, 1, 0)'),
				'updated_at'		=> new Zend_Db_Expr("'{$updatedAt}'")
			);
			if($storeId){	// logging timezone and timezone offset
				$tz = Mage::helper('ts_reports')->getTimezoneOffset($storeId);
				$columns = array_merge($columns, array('tz_name' => new Zend_Db_Expr($adapter->quote($tz['timezone'])), 'tz_offset' => new Zend_Db_Expr($tz['offset'])));
			}
			
			$eavColumns = array(
				'price_code_id'				=> sprintf('MAX(CASE WHEN attribute_code = \'%s\' THEN attribute_id ELSE NULL END)', 'price'),
				'special_price_code_id'		=> sprintf('MAX(CASE WHEN attribute_code = \'%s\' THEN attribute_id ELSE NULL END)', 'special_price'),
				'special_from_date_code_id'	=> sprintf('MAX(CASE WHEN attribute_code = \'%s\' THEN attribute_id ELSE NULL END)', 'special_from_date'),
				'special_to_date_code_id'	=> sprintf('MAX(CASE WHEN attribute_code = \'%s\' THEN attribute_id ELSE NULL END)', 'special_to_date')
			);
			
			$categoryColumns = array(
				'product_id',
				'categories'	=> new Zend_Db_Expr("GROUP_CONCAT(CONCAT('/',category_id,'/') SEPARATOR '')")
			);
			
			$categorySelect = $adapter->select()->from(array('c' => $this->getTable('catalog/category_product')), $categoryColumns)->group('product_id');
			$eavSelect = $adapter->select()->from(array('eav' => $this->getTable('eav/attribute')), $eavColumns);			
			$select = $adapter->select()->from(array('oi' => $this->getTable('sales/order_item')), $columns)
				->joinCross(array('eav' => $eavSelect), array())
				->joinLeft(array('q' => $this->getTable('sales/quote_item')), 'q.item_id = oi.quote_item_id', array())
				->joinLeft(array('o' => $this->getTable('sales/order')), 'o.entity_id = oi.order_id', array())
				->joinLeft(array('product' => 
					$adapter->select()->from(array('product' => $this->getTable('catalog/product')), array('sku', 'entity_id'))),
						'product.sku = oi.sku', array())
				->joinLeft(array('c' => $categorySelect), 'c.product_id = product.entity_id', array())
				->joinLeft(array('p' => 
					$adapter->select()->from(array('p' => $this->getTable('catalog/product').'_decimal'), array('attribute_id', 'entity_id', 'value'))),
						'(p.entity_id = product.entity_id AND p.attribute_id = eav.price_code_id)', array())
				->joinLeft(array('sp' => 
					$adapter->select()->from(array('p' => $this->getTable('catalog/product').'_decimal'), array('attribute_id', 'entity_id', 'value'))),
						'(sp.entity_id = product.entity_id AND sp.attribute_id = eav.special_price_code_id)', array())				
				->joinLeft(array('spf' => $this->getTable('catalog/product') .'_datetime'), '(spf.entity_id = product.entity_id AND spf.attribute_id = eav.special_from_date_code_id)', array())
				->joinLeft(array('spt' => $this->getTable('catalog/product') .'_datetime'), '(spt.entity_id = product.entity_id AND spt.attribute_id = eav.special_to_date_code_id)', array())
				->joinLeft(array('gr'  => $this->getTable('catalog/product') .'_group_price'), '(gr.entity_id = product.entity_id AND gr.customer_group_id = o.customer_group_id AND gr.value = oi.original_price)', array())
				->joinLeft(array('ti'  => $this->getTable('catalog/product') .'_tier_price'), 
						'(ti.entity_id = product.entity_id AND (ti.customer_group_id = o.customer_group_id OR ti.all_groups = 1) AND ti.value = oi.original_price AND oi.qty_ordered >= ti.qty)', array());
			if($orderId) $select->where('o.entity_id = ?', $orderId);
			
			$adapter->query($select->insertFromSelect($this->getMainTable(), array_keys($columns), Varien_Db_Adapter_Interface::INSERT_ON_DUPLICATE));
			$adapter->commit();
			$this->initRulePrices($orderId);
			
		} catch (Exception $e) {
			throw $e;
		}
		
		return $this;	
	}
	
	public function initRulePrices($orderId = null, $importCatalogCheck = null){
		$reportItemPrices = array();
		$orderItemIds = array();		
		
		$adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from(array('rp' => $this->getTable('catalogrule/rule_product')), array('*', 'from_date' => new Zend_Db_Expr('FROM_UNIXTIME(from_time)'), 'to_date' => new Zend_Db_Expr('FROM_UNIXTIME(to_time)') ))
			->joinLeft(array('cr' => $this->getTable('catalogrule/rule')), 'cr.rule_id = rp.rule_id', array())
			->join(array('s' => $this->getTable('core/store')), 's.website_id = rp.website_id', array())
			->join(array('product' => 
					$adapter->select()->from(array('product' => $this->getTable('catalog/product')), array('sku', 'entity_id'))),
						'product.entity_id = rp.product_id', array('sku'))
			->joinCross(array('eav' => 
					$adapter->select()->from(array('eav' => $this->getTable('eav/attribute')), 
						array('price_code_id' => sprintf("MAX(CASE WHEN attribute_code = 'price' THEN attribute_id ELSE NULL END)"))) ), array())
			->joinLeft(array('p' => 
					$adapter->select()->from(array('p' => $this->getTable('catalog/product').'_decimal'), array('attribute_id', 'entity_id', 'value'))),
						'(p.entity_id = product.entity_id AND p.attribute_id = eav.price_code_id)', array('price' => 'p.value'))	
			->joinLeft(array('r' => $this->getTable('catalog/product_relation')), 'r.child_id = product.entity_id', array('parent_id' => 'parent_id'))
			->join(array('ri' => $this->getTable('ts_reports/reportitem')), 
					'ri.sku = product.sku AND ri.customer_group_id = rp.customer_group_id AND ri.store_id = s.store_id', 
					array('original_price', 'order_item_ids' => new Zend_Db_Expr("GROUP_CONCAT(ri.order_item_id SEPARATOR '/')"), 'tz_offset'))
			->join(array('tz' => new Zend_Db_Expr("(SELECT TIMESTAMPDIFF(MINUTE, UTC_TIMESTAMP(), CURRENT_TIMESTAMP())*60 as server_tz)")), null, array())
            ->where('ri.price_type = ?', TS_Reports_Model_Types::UNKNOWN)
            ->where('from_time = 0 OR from_time <= UNIX_TIMESTAMP(ri.order_date) + server_tz + tz_offset')
			->where('to_time = 0 OR to_time >= UNIX_TIMESTAMP(ri.order_date) + server_tz + tz_offset')
            ->order(array('cr.sort_order ASC', 'cr.rule_id ASC')) 			
            ->group('rp.rule_product_id')
            ->group('p.value');
		if($orderId) $select->where('ri.order_id = ?', $orderId);
		// here to put timezone check per store date!
		
		$result = 0;
		if($importCatalogCheck){
			$ruleData =  $adapter->fetchAll( $adapter->select()->from(array('i' => $select))->where('sku IN (?)', array_keys($importCatalogCheck)) );
			if(!empty($ruleData)){
				$ruleDatasForProduct = array();
				foreach($ruleData as $rule) $ruleDatasForProduct[$rule['product_id']][] = $rule;		
				foreach($ruleDatasForProduct as $ruleDatas) $reportItemPrices[] = $this->getPriceFromRuleData($ruleDatas);
			
				$ruleImports = array();
				foreach($ruleDatasForProduct as $key => $ruleData){
					foreach($importCatalogCheck[$rule['sku']] as $importCheck){
						if(!isset($ruleImports[$k])){ 
							$rulePriceInfo = $this->getPriceFromRuleData($ruleData, $importCheck['price'], $importCheck);
							$rulePriceInfo['date_from'] = $importCheck['date_from'];
							$rulePriceInfo['date_to'] = $importCheck['date_to'];
							$ruleImports[] = $rulePriceInfo;		
						}
					}
				}

				$adapter = $this->_getWriteAdapter();
				foreach($ruleImports as $ruleImport){
					$where = sprintf("original_price = %s AND order_item_id IN (%s) AND order_date >= '%s' AND order_date <= '%s' AND order_item_id IN (%s)", 
								$ruleImport['price'], implode(',',$ruleImport['order_item_ids']), $ruleImport['date_from'], $ruleImport['date_to'], implode(',',$reportItemPrice['order_item_ids']));
					$bind = array('price_type' => new Zend_Db_Expr(TS_Reports_Model_Types::CATALOG), 'catalogrule_ids' => $ruleImport['catalogrule_ids']);
					$result += $adapter->update($this->getTable('ts_reports/reportitem'), $bind, $where);
				}
			}
			
		} else {
		
			$ruleData =  $adapter->fetchAll($select);				
			if(!empty($ruleData)){ // JA kui finalPrice ON kataloogireegel!
				$ruleDatasForProduct = array();
				foreach($ruleData as $rule) $ruleDatasForProduct[$rule['product_id']][] = $rule;		
				foreach($ruleDatasForProduct as $ruleDatas) $reportItemPrices[] = $this->getPriceFromRuleData($ruleDatas);

				$adapter = $this->_getWriteAdapter();
				foreach($reportItemPrices as $reportItemPrice){
					$where = sprintf('original_price = %s AND order_item_id IN (%s)', $reportItemPrice['price'], implode(',',$reportItemPrice['order_item_ids']));
					$bind = array('price_type' => new Zend_Db_Expr(TS_Reports_Model_Types::CATALOG), 'catalogrule_ids' => $reportItemPrice['catalogrule_ids']);
					$result += $adapter->update($this->getTable('ts_reports/reportitem'), $bind, $where);
				}
			}
		
		}
		return $result;
	}
	
	private function getPriceFromRuleData($ruleDatas, $basePrice = null, $importCheck = null){
		$orderIds = array();
		$catalogruleIds = array();
		$priceRules = $basePrice;
		
		foreach($ruleDatas as $ruleData){
			if($importCheck && ($importCheck['date_to'] <= $ruleData['from_date'] || $importCheck['date_from'] >= $ruleData['to_date'])) continue;
			if($ruleData['parent_id']){ // is a subproduct, so we apply subproduct rules
				if(!empty($ruleData['sub_simple_action'])) {
					$priceRules = Mage::helper('catalogrule')->calcPriceRule($ruleData['sub_simple_action'], $ruleData['sub_discount_amount'], $priceRules ? $priceRules : $ruleData['price']);
				} else $priceRules = $ruleData['price'];
			} else $priceRules = Mage::helper('catalogrule')->calcPriceRule($ruleData['action_operator'], $ruleData['action_amount'], $priceRules ? $priceRules : $ruleData['price']);
			$orderIds = array_merge($orderIds, explode('/',$ruleData['order_item_ids']));
			$catalogruleIds[$ruleData['rule_id']] = true;
			if($ruleData['action_stop']) break;
		}
		
		return array('price' => $priceRules, 'order_item_ids' => $orderIds, 'catalogrule_ids' => implode(',',array_keys($catalogruleIds)));
	}
	
	
	
	/**
     * Import report item price types & categories
     *
     * @return integer
     */	
	public function import($importItems){
		$result = 0;
		$adapter = $this->_getWriteAdapter();
		foreach($importItems as $importItem){
			$where = sprintf("original_price = %s AND sku = '%s' AND order_date >= '%s' AND order_date <= '%s'", $importItem['price'], $importItem['sku'], $importItem['date_from'], $importItem['date_to']);
			$bind = array('price_override' => new Zend_Db_Expr('price_type'), 'price_type' => new Zend_Db_Expr($importItem['price_type']));
			if(!empty($importItem['categories'])) $bind['categories'] = $importItem['categories'];
			$result += $adapter->update($this->getTable('ts_reports/reportitem'), $bind, $where);
		}
		
		return $result;
	}
	
	/**
     * Mass override of report item price types
     *
     * @return integer
     */	
	public function override($orderItemIds, $priceType){
		$adapter = $this->_getWriteAdapter();
		$where = sprintf("order_item_id IN (%s)", implode(',',$orderItemIds));
		$bind = array('price_override' => new Zend_Db_Expr('price_type'), 'price_type' => new Zend_Db_Expr($priceType));
		return $adapter->update($this->getTable('ts_reports/reportitem'), $bind, $where);
	}
	
	/**
     * Mass reset of report item price types
     *
     * @return integer
     */	
	public function reset($orderItemIds){
		$adapter = $this->_getWriteAdapter();
		$where = sprintf("order_item_id IN (%s)", implode(',',$orderItemIds));
		$bind = array('price_type' => new Zend_Db_Expr('price_override'), 'price_override' => new Zend_Db_Expr("NULL"));
		return $adapter->update($this->getTable('ts_reports/reportitem'), $bind, $where);
	}
	
	
}
