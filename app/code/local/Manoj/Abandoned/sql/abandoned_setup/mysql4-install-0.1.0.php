<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS `abandoned_cart` (
  `abandoned_cart_id` int(11) NOT NULL AUTO_INCREMENT,
  `email_id` varchar(255) DEFAULT NULL,
  `quote_id` int(11) DEFAULT NULL,
  `is_email_send` int(11) DEFAULT NULL,
  `is_purchase` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `product_ids` varchar(4000) DEFAULT NULL,
  `subtotal` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`abandoned_cart_id`))
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 