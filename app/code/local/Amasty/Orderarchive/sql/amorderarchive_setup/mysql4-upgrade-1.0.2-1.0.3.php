<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT

alter table amasty_orderachive_sales_flat_order_grid_archive add tracking_number int(20) not null,
add order_placed_country varchar(255) not null,
add customer_order_increment_id varchar(255) not null,
add country_id varchar(255) not null,
add echeck_transactionid varchar(255) not null,
add browser_details varchar(255) not null,
add customer_group_id varchar(255) not null,
add customer_telephone varchar(255) not null,
add esafepayment_transactionid varchar(255) not null;

SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();