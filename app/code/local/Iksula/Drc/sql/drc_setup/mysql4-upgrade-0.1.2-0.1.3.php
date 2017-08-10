<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
ALTER TABLE sales_flat_order add voucher_transaction_id varchar(255) not null;
ALTER TABLE sales_flat_order_grid add voucher_transaction_id varchar(255) not null;
		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
