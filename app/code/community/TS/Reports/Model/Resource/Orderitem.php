<?php

class TS_Reports_Model_Resource_Orderitem extends TS_Reports_Model_Resource_Abstract {

	public function _construct(){
		$this->_init('ts_reports/orderitem', 'order_item_id');
	}

	
	public function init($refreshDate = null){
		$resultOrderIds = null;
		$adapter = $this->_getWriteAdapter();

		try {
			if($refreshDate){
				$select = $adapter->select()->from(array('oi' => $this->getTable('sales/order_item')), array('item_id' => new Zend_Db_Expr('DISTINCT oi.item_id'))) //upgrade order rows
					->join(array('o' => $this->getTable('sales/order')), 'oi.order_id = o.entity_id', array())
					->joinLeft(array('i' => $this->getTable('sales/invoice')), 'i.order_id = o.entity_id', array())
					->where('oi.created_at >= ? OR oi.updated_at >= ?
						OR o.created_at >= ? OR o.updated_at >= ?
						OR i.created_at >= ? OR i.updated_at >= ?', $refreshDate, $refreshDate, $refreshDate, $refreshDate, $refreshDate, $refreshDate);
				
				$query = $adapter->query($select);
				$resultOrderIds = array_map(function($entry){ return $entry['item_id']; }, $query->fetchAll()); //testi isseti ja kui query on tyhi!
				
				$adapter->delete( $this->getMainTable(), array('order_item_id IN(?)' => $resultOrderIds) );
				$adapter->commit();
			}
		
            $periodExpr = $adapter->getDatePartSql( $this->getOffsetQuery($adapter, 'o.created_at', $this->getOffset()) );
			
			$base_to_global_rate		= $adapter->getIfNullSql('o.base_to_global_rate', 1);
			$base_discount_amount		= $adapter->getIfNullSql('oi.base_discount_amount', 0);
			$base_discount_invoiced		= $adapter->getIfNullSql('oi.base_discount_invoiced', 0);
			$base_discount_refunded		= $adapter->getIfNullSql('oi.base_discount_refunded', 0);				
			$base_tax_amount			= $adapter->getIfNullSql('oi.base_tax_amount', 0);
			$base_tax_canceled			= $adapter->getIfNullSql('oi.tax_canceled', 0);
			$base_tax_refunded			= $adapter->getIfNullSql('oi.base_tax_refunded', 0);
			$base_tax_invoiced			= $adapter->getIfNullSql('oi.base_tax_invoiced', 0);				
			$base_grand_total			= $adapter->getIfNullSql('oi.base_row_total_incl_tax', 0);
			$base_total_refunded_excl_tax = $adapter->getIfNullSql('oi.base_amount_refunded', 0);				 
			$base_total_invoiced_excl_tax =	$adapter->getIfNullSql('oi.base_row_invoiced', 0);
			$base_discount_canceled = sprintf('(%s * (abs(%s) / %s))',			
				$adapter->getIfNullSql('oi.qty_canceled', 0),
				$adapter->getIfNullSql('oi.base_discount_amount',0),	
				$adapter->getIfNullSql('oi.qty_ordered',0)
			);
			$base_total_canceled = sprintf('(%s * (%s - (abs(%s) / %s)))',
				$adapter->getIfNullSql('oi.qty_canceled', 0),
				$adapter->getIfNullSql('oi.price_incl_tax',0),
				$adapter->getIfNullSql('oi.base_discount_amount',0),
				$adapter->getIfNullSql('oi.qty_ordered',0)
			);
			$base_total_paid_cost = sprintf('(%s * (%s - %s))',
				$adapter->getIfNullSql('oi.base_cost', 0),
				$adapter->getIfNullSql('oi.qty_invoiced',0),
				$adapter->getIfNullSql('oi.qty_refunded',0)
			);
			$base_total_paid_excl_tax = sprintf('IF(%s = \'%s\', SUM(%s), 0)',
				'i.state',
				Mage_Sales_Model_Order_Invoice::STATE_PAID,
				$adapter->getIfNullSql('ii.base_row_total',0)
			);
			$base_paid_tax = sprintf('IF(%s = \'%s\', SUM(%s), 0)',
				'i.state',
				Mage_Sales_Model_Order_Invoice::STATE_PAID,
				$adapter->getIfNullSql('ii.base_tax_amount',0)
			);
			$base_grand_total = $adapter->getIfNullSql('oi.base_row_total_incl_tax', 0);
			$base_total_refunded_excl_tax = $adapter->getIfNullSql('oi.base_amount_refunded', 0);
			$base_total_invoiced_excl_tax = $adapter->getIfNullSql('oi.base_row_invoiced', 0);
			
			$columns = array(
				'order_item_id'					 => 'oi.item_id',
                'period'                         => $periodExpr,
				'order_status'					 => 'o.status',
				'total_qty_ordered'				 => new Zend_Db_Expr("{$adapter->getIfNullSql('oi.qty_ordered', 0)}"),
				'total_qty_ordered_actual'		 => new Zend_Db_Expr("{$adapter->getIfNullSql('oi.qty_ordered', 0)} - {$adapter->getIfNullSql('oi.qty_canceled', 0)}"),
				'total_qty_invoiced'			 => new Zend_Db_Expr("{$adapter->getIfNullSql('oi.qty_invoiced', 0)}"),
				'total_qty_invoiced_actual'		 => new Zend_Db_Expr("{$adapter->getIfNullSql('oi.qty_invoiced', 0)} - {$adapter->getIfNullSql('oi.qty_refunded', 0)}"),
				
				'total_income_amount'			 => new Zend_Db_Expr(
					sprintf('((%s - %s)* %s)',
						$base_grand_total,
						$base_total_canceled,
						$base_to_global_rate
					)
				),
				'total_revenue_amount'			 => new Zend_Db_Expr(
					sprintf('((%s - %s)* %s)',
						$base_total_invoiced_excl_tax,
						$base_total_refunded_excl_tax,
						$base_to_global_rate
					)
				),
				'total_invoiced_amount'			 => new Zend_Db_Expr(
					sprintf('((%s - %s - %s) * %s)',
						$base_total_paid_excl_tax,
						$base_total_refunded_excl_tax,
						$base_total_paid_cost,
						$base_to_global_rate
					)
				),
				'total_canceled_amount'			 => new Zend_Db_Expr(
					sprintf('(%s * %s)',
						$base_total_canceled,
						$base_to_global_rate
					)
				),
				'total_paid_amount'			 => new Zend_Db_Expr(
					sprintf('((%s + %s) * %s)',
						$base_total_paid_excl_tax,
						$base_paid_tax,
						$base_to_global_rate
					)
				),
				'total_refunded_amount'			 => new Zend_Db_Expr(
					sprintf('((%s + %s) * %s)',
						$base_total_refunded_excl_tax,
						$adapter->getIfNullSql('oi.base_tax_refunded', 0),
						$base_to_global_rate
					)
				),
				'total_tax_amount'			 => new Zend_Db_Expr(
					sprintf('((%s - %s) * %s)',
						$adapter->getIfNullSql('oi.base_tax_amount', 0),
						$adapter->getIfNullSql('oi.tax_canceled', 0),
						$base_to_global_rate
					)
				),
				'total_tax_amount_actual'			 => new Zend_Db_Expr(
					sprintf('((%s - %s) * %s)',
						$adapter->getIfNullSql('oi.base_tax_invoiced', 0),
						$adapter->getIfNullSql('oi.base_tax_refunded', 0),
						$base_to_global_rate
					)
				),
				'total_discount_amount'			 => new Zend_Db_Expr(
					sprintf('((abs(%s) - %s) * %s)',
						$adapter->getIfNullSql('oi.base_discount_amount', 0),
						$base_discount_canceled,
						$base_to_global_rate
					)
				),
				'total_discount_amount_actual'			 => new Zend_Db_Expr(
					sprintf('((%s - %s) * %s)',
						$adapter->getIfNullSql('oi.base_discount_invoiced', 0),
						$adapter->getIfNullSql('oi.base_discount_refunded', 0),
						$base_to_global_rate
					)
				)
			);
			$select = $adapter->select()->from(array('oi' => $this->getTable('sales/order_item')), $columns)
				->join(array('o' => $this->getTable('sales/order')), 'oi.order_id = o.entity_id', array())
				->joinLeft(array('ii' => $this->getTable('sales/invoice_item')), 'oi.item_id = ii.order_item_id ', array())
				->joinLeft(array('i' => $this->getTable('sales/invoice')), 'ii.parent_id = i.entity_id', array())
				->where('o.state NOT IN (?)', array(
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                    Mage_Sales_Model_Order::STATE_NEW
                )) // from original aggregated/live table creation
				->group(array('oi.item_id'));
				
			if($refreshDate){
				$subselect = $adapter->select()->from(array('sub_oi' => $this->getMainTable()), array('order_item_id'))->where('sub_oi.order_item_id = oi.item_id');
				$select->where("NOT EXISTS ?", $subselect);			
			}
			
			$adapter->query($select->insertFromSelect($this->getMainTable(), array_keys($columns), Varien_Db_Adapter_Interface::INSERT_ON_DUPLICATE));
			$adapter->commit();
		} catch (Exception $e) {
			throw $e;
		}
		
		return $this;	
	}

}
