<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table limitedsupply_info(id int not null auto_increment, product_sku varchar(100), product_name varchar(100), email varchar(100), comment varchar(1000), is_active varchar(1000) ,date DATETIME default '0000-00-00 00:00:00' ,primary key(id));
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 