<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
alter table ordertracking_log add shipping_part varchar(255) not null;
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 