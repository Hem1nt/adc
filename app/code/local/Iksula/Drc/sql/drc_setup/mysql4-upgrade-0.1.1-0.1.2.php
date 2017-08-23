<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table dcr_request_email(
id int not null auto_increment, 
requested_by varchar(200),
email varchar(200),
created_at timestamp,
primary key(id));
		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 