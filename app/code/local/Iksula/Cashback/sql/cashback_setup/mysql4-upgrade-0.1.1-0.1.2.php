<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
alter table cashback add (created_at varchar(255),coupon_active_date varchar(255),coupon_expire_date varchar(255),use_month varchar(255));
		
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
	 