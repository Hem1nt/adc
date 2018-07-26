<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE `abandoned_cart_order` (
 `abandoned_order_id` int(255) NOT NULL AUTO_INCREMENT,
 `order_number` varchar(255) DEFAULT NULL,
 `email_id` varchar(255) DEFAULT NULL,
 `created_time` datetime DEFAULT NULL,
 `update_time` datetime DEFAULT NULL,
 PRIMARY KEY (`abandoned_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SQLTEXT;

$installer->run($sql);
$installer->endSetup();
	 