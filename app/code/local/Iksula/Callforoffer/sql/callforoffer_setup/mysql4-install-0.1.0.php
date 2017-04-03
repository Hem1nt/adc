<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table if not exists callforoffers(id int not null auto_increment,
cust_name varchar(100),
customerid varchar(100),
customertelephoneno  varchar(100),
status varchar(100),
customertype varchar(100),
primary key(id));
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 