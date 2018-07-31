<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table customerclickinfo(customeform_id int not null auto_increment, customer_name varchar(100), customer_email varchar(255),customer_mobileno varchar(255),customer_time varchar(255) ,customer_comment longtext,customer_calling_status int, `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,  last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,primary key(customeform_id));

		
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
	 