<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
ALTER TABLE `cashback` CHANGE `created_at` `created_at` DATE NULL DEFAULT NULL;
ALTER TABLE `cashback` CHANGE `coupon_active_date` `coupon_active_date` DATE NULL DEFAULT NULL;
ALTER TABLE `cashback` CHANGE `coupon_expire_date` `coupon_expire_date` DATE NULL DEFAULT NULL;
		
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
	 