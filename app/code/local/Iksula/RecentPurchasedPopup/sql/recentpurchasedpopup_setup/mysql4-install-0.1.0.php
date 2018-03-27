<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table recentpurchasedpopup(popup_id int not null auto_increment, product_sku varchar(100), primary key(popup_id));
		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 