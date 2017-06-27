<?php
$installer = $this;
$installer->startSetup();
$installer->run(" 
ALTER TABLE `{$installer->getTable('sales/order_grid')}` ADD `major_comment` VARCHAR(255) NOT NULL;
");
$installer->endSetup();
	 