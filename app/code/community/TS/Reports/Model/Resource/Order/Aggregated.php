<?php

class TS_Reports_Model_Resource_Order_Aggregated extends TS_Reports_Model_Resource_Abstract {

	public function _construct(){
		$this->_init('ts_reports/order_aggregated', 'period_status');
	}

    public function init($refreshDate = null){
        $adapter = $this->_getWriteAdapter();
		
        try {
            $periodExpr = $adapter->getDatePartSql( $this->getOffsetQuery($adapter, 'o.created_at', $this->getOffset()) );
			
			if(empty($refreshDate)) $adapter->truncateTable($this->getMainTable());
			else {
				$select = $adapter->select()->from(array('o' => $this->getTable('sales/order')), array('period' => new Zend_Db_Expr("DISTINCT {$periodExpr}"))) //upgrade order rows
					->join(array('oi' => $this->getTable('sales/order_item')), 'oi.order_id = o.entity_id',array())
					->joinLeft(array('i' => $this->getTable('sales/invoice')), 'i.order_id = o.entity_id', array())
					->where('oi.created_at >= ? OR oi.updated_at >= ?
						OR o.created_at >= ? OR o.updated_at >= ?
						OR i.created_at >= ? OR i.updated_at >= ?', $refreshDate, $refreshDate, $refreshDate, $refreshDate, $refreshDate, $refreshDate);
				$query = $adapter->query($select);
				$resultDates = array_map(function($entry){ return $entry['period']; }, $query->fetchAll()); //testi isseti ja kui query on tyhi!
				
				$adapter->delete( $this->getMainTable(), array('period IN(?)' => $resultDates) );
				$adapter->commit();
			}
	
			
            $columnsOrderItem = array(
				'order_id'				=> 'oi.order_id',
				'item_id'				=> 'oi.item_id',
                'base_total_cost_refunded'	=> new Zend_Db_Expr(
					sprintf('SUM(%s * %s)',
						$adapter->getIfNullSql('oi.qty_refunded',0),
						$adapter->getIfNullSql('oi.base_cost',0)
					)
				),
				'total_qty_invoiced'	=> new Zend_Db_Expr(
					sprintf('SUM(%s)',
						$adapter->getIfNullSql('oi.qty_invoiced',0)
					)
				),
				'total_qty_canceled'	=> new Zend_Db_Expr(
					sprintf('SUM(%s)',
						$adapter->getIfNullSql('oi.qty_canceled',0)
					)
				),
				'total_qty_refunded'	=> new Zend_Db_Expr(
					sprintf('SUM(%s)',
						$adapter->getIfNullSql('oi.qty_refunded',0)
					)
				)
			);
			
            $columnsInvoice = array(
				'entity_id'				=> 'i.entity_id',
				'order_id'				=> 'i.order_id',
				'state'					=> 'i.state',
				'total_tax_paid'		=> new Zend_Db_Expr(
					sprintf('SUM(IF(%s = \'%s\', %s, 0))',
						'i.state',
						Mage_Sales_Model_Order_Invoice::STATE_PAID,
						$adapter->getIfNullSql('i.base_tax_amount',0)
					)
				),
                'base_total_cost_paid'	=> new Zend_Db_Expr(
					sprintf('IF(%s = %s, %s, 0)', //if state_paid then there is base_total_cost
						'i.state',
						Mage_Sales_Model_Order_Invoice::STATE_PAID,
						'ii.base_total_cost'
					)
				)
			);
			
            $columnsInvoiceItem = array(
				'parent_id'				=> 'ii.parent_id',
                'base_total_cost'		=> new Zend_Db_Expr(
					sprintf('SUM(%s * %s)',
						$adapter->getIfNullSql('ii.base_cost',0),
						'ii.qty'
					)
				)
			);
            // Columns list
            $columns = array(
                // convert dates from UTC to current admin timezone
                'period_status'                  => new Zend_Db_Expr("CONCAT({$periodExpr},'_',o.status)"),
                'period'                         => $periodExpr,
                'store_id'                       => 'o.store_id',
                'order_status'                   => 'o.status',
                'orders_count'                   => new Zend_Db_Expr('COUNT(o.entity_id)'),
                'total_qty_ordered'              => new Zend_Db_Expr('SUM(o.total_qty_ordered)'), 
                'total_qty_ordered_actual'       => new Zend_Db_Expr('SUM(o.total_qty_ordered - oi.total_qty_canceled)'), // - qty_canceled!!! (from columns described above)
                'total_qty_invoiced'             => new Zend_Db_Expr('SUM(oi.total_qty_invoiced)'),
                'total_qty_invoiced_actual'      => new Zend_Db_Expr('SUM(oi.total_qty_invoiced - oi.total_qty_refunded)'),
                'total_income_amount'            => new Zend_Db_Expr(
                    sprintf('SUM((%s - %s) * %s)',
                        $adapter->getIfNullSql('o.base_grand_total', 0),
                        $adapter->getIfNullSql('o.base_total_canceled',0),
                        $adapter->getIfNullSql('o.base_to_global_rate',0)
                    )
                ),
                'total_revenue_amount'           => new Zend_Db_Expr(
                    sprintf('SUM((%s - %s - %s - (%s - %s - %s)) * %s)',
                        $adapter->getIfNullSql('o.base_total_invoiced', 0),
                        $adapter->getIfNullSql('o.base_tax_invoiced', 0),
                        $adapter->getIfNullSql('o.base_shipping_invoiced', 0),
                        $adapter->getIfNullSql('o.base_total_refunded', 0),
                        $adapter->getIfNullSql('o.base_tax_refunded', 0),
                        $adapter->getIfNullSql('o.base_shipping_refunded', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_profit_amount'            => new Zend_Db_Expr(
                    sprintf('SUM((%s - %s) - (%s - %s) - (%s - %s) - IF(%s > 0, (%s - %s), 0) * %s)',
                        $adapter->getIfNullSql('o.base_total_paid', 0),
						$adapter->getIfNullSql('i.total_tax_paid', 0),
                        $adapter->getIfNullSql('i.base_total_cost_paid', 0), //we need to deduct refunded_cost
                        $adapter->getIfNullSql('oi.base_total_cost_refunded', 0), //we need to deduct refunded_cost
                        $adapter->getIfNullSql('o.base_total_refunded', 0),
                        $adapter->getIfNullSql('o.base_tax_refunded', 0),
						$adapter->getIfNullSql('o.base_total_paid',0),
						$adapter->getIfNullSql('o.base_shipping_invoiced',0), //if there is invoice, shipping price is included in the first one
                        $adapter->getIfNullSql('o.base_shipping_refunded', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_invoiced_amount'          => new Zend_Db_Expr(
                    sprintf('SUM(%s * %s)',
                        $adapter->getIfNullSql('o.base_total_invoiced', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_canceled_amount'          => new Zend_Db_Expr(
                    sprintf('SUM(%s * %s)',
                        $adapter->getIfNullSql('o.base_total_canceled', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_paid_amount'              => new Zend_Db_Expr(
                    sprintf('SUM(%s * %s)',
                        $adapter->getIfNullSql('o.base_total_paid', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_refunded_amount'          => new Zend_Db_Expr(
                    sprintf('SUM(%s * %s)',
                        $adapter->getIfNullSql('o.base_total_refunded', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_tax_amount'               => new Zend_Db_Expr(
                    sprintf('SUM((%s - %s) * %s)',
                        $adapter->getIfNullSql('o.base_tax_amount', 0),
                        $adapter->getIfNullSql('o.base_tax_canceled', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_tax_amount_actual'        => new Zend_Db_Expr(
                    sprintf('SUM((%s -%s) * %s)',
                        $adapter->getIfNullSql('o.base_tax_invoiced', 0),
                        $adapter->getIfNullSql('o.base_tax_refunded', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_shipping_amount'          => new Zend_Db_Expr(
                    sprintf('SUM((%s - %s) * %s)',
                        $adapter->getIfNullSql('o.base_shipping_amount', 0),
                        $adapter->getIfNullSql('o.base_shipping_canceled', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_shipping_amount_actual'   => new Zend_Db_Expr(
                    sprintf('SUM((%s - %s) * %s)',
                        $adapter->getIfNullSql('o.base_shipping_invoiced', 0),
                        $adapter->getIfNullSql('o.base_shipping_refunded', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_discount_amount'          => new Zend_Db_Expr(
                    sprintf('SUM((ABS(%s) - %s) * %s)',
                        $adapter->getIfNullSql('o.base_discount_amount', 0),
                        $adapter->getIfNullSql('o.base_discount_canceled', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                ),
                'total_discount_amount_actual'   => new Zend_Db_Expr(
                    sprintf('SUM((%s - %s) * %s)',
                        $adapter->getIfNullSql('o.base_discount_invoiced', 0),
                        $adapter->getIfNullSql('o.base_discount_refunded', 0),
                        $adapter->getIfNullSql('o.base_to_global_rate', 0)
                    )
                )
            );
			
			$selectOrderItem = $adapter->select()->from(array('oi' => $this->getTable('sales/order_item')), $columnsOrderItem)
				->group(array('oi.order_id'));
			$selectInvoiceItem = $adapter->select()->from(array('ii' => $this->getTable('sales/invoice_item')), $columnsInvoiceItem)
				->group(array('ii.parent_id'));
				
			$selectInvoice = $adapter->select()->from(array('i' => $this->getTable('sales/invoice')), $columnsInvoice)
				->join(array('ii' => $selectInvoiceItem), 'ii.parent_id = i.entity_id', array())
				->group(array('i.order_id'));
				
			$select = $adapter->select()->from(array('o' => $this->getTable('sales/order')), $columns)
				->join(array('oi' => $selectOrderItem), 'oi.order_id = o.entity_id', array())
				->joinLeft(array('i' => $selectInvoice), 'o.entity_id = i.order_id', array())
				->where('o.state NOT IN (?)', array(
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                    Mage_Sales_Model_Order::STATE_NEW
                )) // from original aggregated/live table creation
				->group(array('period_status'));
				
			if($refreshDate){
				$subselect = $adapter->select()->from(array('sub_o' => $this->getMainTable()), array('period'))->where('sub_o.period = ?', $periodExpr);
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
