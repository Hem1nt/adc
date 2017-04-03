<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table if not exists refillreminder(
reminder_id int not null auto_increment,
customer_email varchar(300),
product_sku varchar(20),
reminder_days int,
remind_flag tinyint(2),
customer_telephone varchar(15),
comment text,
product_name text,
created_date timestamp,
modified_date timestamp,
last_mail_sent timestamp,
next_mail_on timestamp,
mail_sent_count int,
customer_name varchar(100),
time_to_call varchar(100),
status int(10),
primary key(reminder_id)
);

create table if not exists orderreminder(
order_inc_id int not null auto_increment,
customer_email varchar(300),
product_sku varchar(2000),
reminder_days int,
remind_flag tinyint(2),
customer_telephone varchar(15),
comment text,
product_name text,
created_date timestamp,
modified_date timestamp,
last_mail_sent timestamp,
next_mail_on timestamp,
mail_sent_count int,
customer_name varchar(100),
order_id varchar(20),
order_total float,
time_to_call varchar(100),
status int(10),
primary key(order_inc_id)
);
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 