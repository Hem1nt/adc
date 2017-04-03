<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table customerinfo(id int not null auto_increment, name varchar(100), email varchar(255), dob date, anniversary date, primary key(id));

		
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
	 