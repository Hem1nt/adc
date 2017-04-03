<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table if not exists backendfaq(
id int not null auto_increment,
question varchar(5000), 
answer varchar(5000), 
date date, 
username varchar(100), 
topic varchar(100), 
primary key(id));

		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 