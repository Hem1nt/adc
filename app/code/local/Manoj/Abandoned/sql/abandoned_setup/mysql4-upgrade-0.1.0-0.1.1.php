<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
ALTER TABLE `sales_flat_quote` ADD `abandoned_page_capture` VARCHAR( 255 ) NOT NULL; 
ALTER TABLE `abandoned_cart` ADD `abandoned_page_capture` VARCHAR( 255 ) NOT NULL AFTER `quote_id`;
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 