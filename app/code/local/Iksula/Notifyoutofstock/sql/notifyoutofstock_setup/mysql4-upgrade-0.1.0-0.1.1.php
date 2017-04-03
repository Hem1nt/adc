<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE {$this->getTable('notifyoutofstock')} ADD COLUMN `count` int(11) NULL AFTER `product_id`
	");
$installer->endSetup();