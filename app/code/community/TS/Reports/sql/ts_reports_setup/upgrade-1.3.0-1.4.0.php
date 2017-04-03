<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()
	->addColumn(
		$installer->getTable('ts_reports/orderitem'), 
		'total_qty_ordered_actual',  
		array(
			'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
			'length'    => '12,4',
			'nullable'  => false,
			'default'	=> '0.0000',
			'comment'   => 'Total Qty Ordered (Actual)'
		)
	);	
$installer->getConnection()->addColumn(
		$installer->getTable('ts_reports/orderitem'), 
		'total_qty_invoiced_actual',  
		array(
			'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
			'length'    => '12,4',
			'nullable'  => false,
			'default'	=> '0.0000',
			'comment'   => 'Total Qty Invoiced (Actual)'
		)
	);

$installer->getConnection()
	->addColumn(
		$installer->getTable('ts_reports/order_aggregated'), 
		'total_qty_ordered_actual',  
		array(
			'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
			'length'    => '12,4',
			'nullable'  => false,
			'default'	=> '0.0000',
			'comment'   => 'Total Qty Ordered (Actual)'
		)
	);
$installer->getConnection()->addColumn(
		$installer->getTable('ts_reports/order_aggregated'), 
		'total_qty_invoiced_actual',  
		array(
			'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
			'length'    => '12,4',
			'nullable'  => false,
			'default'	=> '0.0000',
			'comment'   => 'Total Qty Invoiced (Actual)'
		)
	);
	
$installer->endSetup();
