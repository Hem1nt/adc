<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table outofstocksubscription_info(id int not null auto_increment, product_sku varchar(100), product_name varchar(100), email varchar(100), notification_type varchar(1000), date DATETIME default '0000-00-00 00:00:00' ,primary key(id));
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
