<?php

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();
$tables = array();

$table['reportitem'] = $installer->getTable('ts_reports/reportitem');
$table['orderitem'] = $installer->getTable('ts_reports/orderitem');
$table['order_aggregated'] = $installer->getTable('ts_reports/order_aggregated');


foreach($table as $t){
	if($connection->isTableExists($t)){
		$connection->dropTable($t);
		$connection->commit();
	}
}

$tables[] = $installer->getConnection()->newTable($table['reportitem'])
	->addColumn('order_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned' => true,
		'nullable' => false,
        'primary' => true
	), 'Order Item ID')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned' => true,
		'nullable' => false,		
	), 'Order ID')
	->addColumn('order_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
		'nullable' => true,
		'default' => NULL
	), 'Order Date')
	->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
        'unsigned' => true,
		'nullable' => false,		
	), 'Store Id')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
	), 'SKU')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned' => true,
		'nullable' => false,		
	), 'Order ID')
	->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
		'nullable' => true,
	), 'Product name')
	->addColumn('categories', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
		'nullable' => true,
	), 'Categories')
	->addColumn('original_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Price Excl Tax')
	->addColumn('base_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Price Excl Tax')
	->addColumn('price_incl_tax', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Price Incl Tax')
	->addColumn('price_type', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
	), 'Price type')
	->addColumn('price_override', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => true,
	), 'Price type before override')
	->addColumn('catalogrule_ids', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => true,
	), 'Catalog rule IDs')
    ->addColumn('cart', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
		'nullable' => false,
		'default' => false,
	), 'Cart discount')
	->addColumn('bug', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
	), 'Bug (1 cent)')
	->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 6, array(
		'nullable' => false,
	), 'Customer Group ID')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
		'nullable' => false,
		'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE,
	), 'Timestamp')
    ->setComment('TS Reports Item');



$tables[] = $installer->getConnection()->newTable($table['orderitem'])
	->addColumn('order_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity' => false,
		'unsigned' => true,
        'nullable' => false,
        'primary' => true        
	), 'Order Item ID')
	->addColumn('period', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        'nullable' => false
	), 'Order Created Date')
	->addColumn('order_status', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
		'nullable'	=> false,
		'default'	=> null
	), 'Order Status')
	->addColumn('total_qty_ordered', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Qty Ordered')
	->addColumn('total_qty_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Qty Invoiced')
	->addColumn('total_income_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Income Amount')
	->addColumn('total_revenue_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Revenue Amount')
	->addColumn('total_profit_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Profit Amount')
	->addColumn('total_invoiced_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Invoiced Amount')	
	->addColumn('total_canceled_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Canceled Amount')
	->addColumn('total_paid_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Paid Amount')
	->addColumn('total_refunded_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Refunded Amount')
	->addColumn('total_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Tax Amount')
	->addColumn('total_tax_amount_actual', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Tax Amount Actual')
	->addColumn('total_shipping_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Shipping Amount')	
	->addColumn('total_shipping_amount_actual', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Shipping Amount Actual')
	->addColumn('total_discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Discount Amount')
	->addColumn('total_discount_amount_actual', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Discount Amount Actual')
    ->setComment('TS Reports Order Item table');


$tables[] = $installer->getConnection()->newTable($table['order_aggregated'])
	->addColumn('period_status', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'primary' => true,
        'nullable' => false
	), 'Period + Status as ID')
	->addColumn('period', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        'nullable' => false
	), 'Order Created Date')
	->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
        'unsigned' => true,
		'nullable' => false,		
	), 'Store Id')
	->addColumn('order_status', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
		'nullable'	=> false,
		'default'	=> null
	), 'Order Status')
	->addColumn('orders_count', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Order Status')
	->addColumn('total_qty_ordered', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Qty Ordered')
	->addColumn('total_qty_invoiced', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Qty Invoiced')
	->addColumn('total_income_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Income Amount')
	->addColumn('total_revenue_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Revenue Amount')
	->addColumn('total_profit_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Profit Amount')
	->addColumn('total_invoiced_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Invoiced Amount')	
	->addColumn('total_canceled_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Canceled Amount')
	->addColumn('total_paid_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Paid Amount')
	->addColumn('total_refunded_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Refunded Amount')
	->addColumn('total_tax_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Tax Amount')
	->addColumn('total_tax_amount_actual', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Tax Amount Actual')
	->addColumn('total_shipping_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Shipping Amount')	
	->addColumn('total_shipping_amount_actual', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Shipping Amount Actual')
	->addColumn('total_discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Discount Amount')
	->addColumn('total_discount_amount_actual', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(12,4), array(
		'nullable'	=> false,
		'default'	=> '0.0000',
	), 'Total Discount Amount Actual')
    ->setComment('TS Reports Order Aggregated table');
	
foreach($tables as $table){
	$installer->getConnection()->createTable($table);
}

$installer->endSetup();
