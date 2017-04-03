<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
alter table cashback add rule_order_id varchar(255);
		
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
	 