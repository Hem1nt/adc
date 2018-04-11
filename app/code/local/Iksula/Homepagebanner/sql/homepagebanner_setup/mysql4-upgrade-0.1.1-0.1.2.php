<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
alter table homepagebanner add active_date date,
add deactive_date date;

		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 