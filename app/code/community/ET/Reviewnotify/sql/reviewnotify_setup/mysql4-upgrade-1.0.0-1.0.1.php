<?php
$installer = $this;
$installer->startSetup();
$installer->run("ALTER TABLE review_detail ADD COLUMN ip_tracking VARCHAR(255) NULL");
$installer->endSetup();
	 
