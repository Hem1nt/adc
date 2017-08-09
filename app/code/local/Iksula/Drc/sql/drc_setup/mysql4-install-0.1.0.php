<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
ALTER TABLE sales_flat_quote_payment add voucher_number varchar(255) not null;
ALTER TABLE sales_flat_quote_payment add voucher_cvv varchar(255) not null;
ALTER TABLE sales_flat_order_payment add voucher_number varchar(255) not null;
ALTER TABLE sales_flat_order_payment add voucher_cvv varchar(255) not null;
		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 