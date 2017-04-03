<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table cashback(id int not null auto_increment, user_id int(11),user_email varchar(255),coupon_code varchar(255),amount varchar(255),uses int(11), primary key(id));
		
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
	 