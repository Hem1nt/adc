<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table bulk_prescription(id int not null auto_increment,
order_id varchar(255),
customer_name varchar(255), 
primary_physicians_name varchar(255),
physicians_telephone_no varchar(255),
drug_allergies varchar(255),
current_medications_allergies varchar(255),
current_treatments varchar(255),
smoke varchar(255),
drink varchar(255),
dob varchar(255),
pregnant varchar(255),
prescription varchar(1000),
primary key(id));
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 