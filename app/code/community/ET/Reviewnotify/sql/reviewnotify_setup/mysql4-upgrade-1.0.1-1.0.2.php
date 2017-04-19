<?php
$installer = $this;
$installer->startSetup();
$installer->run("ALTER TABLE sales_flat_order_item ADD COLUMN is_reviwed int(11) default 0");
$installer->endSetup();
	 
