<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table customerclickinfo(customeform_id int not null auto_increment, customer_name varchar(100), customer_email varchar(255),customer_mobileno int,customer_time varchar(255) ,customer_comment longtext, `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,  last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,primary key(customeform_id));

		
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
	 