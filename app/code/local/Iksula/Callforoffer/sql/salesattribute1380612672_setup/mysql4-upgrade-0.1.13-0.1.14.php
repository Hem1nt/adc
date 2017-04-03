<?php
$installer = $this;
$installer->startSetup();
// $installer->addAttribute("order", "order_platform", array("type"=>"varchar"));
$installer->run(" 
ALTER TABLE `{$installer->getTable('sales/order')}` ADD `echeck_transactionid` VARCHAR(255) NOT NULL;	
ALTER TABLE `{$installer->getTable('sales/order_grid')}` ADD `order_placed_country` VARCHAR(255) NOT NULL;
ALTER TABLE `{$installer->getTable('sales/order_grid')}` ADD `customer_order_increment_id` VARCHAR(255) NOT NULL;
ALTER TABLE `{$installer->getTable('sales/order_grid')}` ADD `country_id` VARCHAR(255) NOT NULL;
ALTER TABLE `{$installer->getTable('sales/order_grid')}` ADD `echeck_transactionid` VARCHAR(255) NOT NULL;
ALTER TABLE `{$installer->getTable('sales/order_grid')}` ADD `browser_details` text NOT NULL;
ALTER TABLE `{$installer->getTable('sales/order_grid')}` ADD `customer_group_id` VARCHAR(255) NOT NULL;
");
$installer->endSetup();