<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS `birthday` (
  `birthday_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `first_name` varchar(256) NOT NULL,
  `last_name` varchar(256) NOT NULL,
  `email` varchar(256) NOT NULL,
  `coupon` varchar(256) NOT NULL,
  `birthdate` date NOT NULL,
  `anniversary` date NOT NULL,
  `customer_group` varchar(256) NOT NULL,
  `coupon_created_date` datetime NOT NULL,
  `coupon_expire_date` datetime NOT NULL,
  `coupon_status` int(11) NOT NULL,
`date_mail_to_send` datetime NOT NULL,
  `email_send` int(11) NOT NULL,
  `no_of_email_send` int(11) NOT NULL,
  PRIMARY KEY (`birthday_id`)
) 
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 