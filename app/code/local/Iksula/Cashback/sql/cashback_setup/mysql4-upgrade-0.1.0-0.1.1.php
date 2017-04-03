<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
alter table cashback add order_id varchar(255);
		
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
	 