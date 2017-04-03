<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS `notifyoutofstock` (
  `notify_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(12) NOT NULL,
  `product_sku` int(12) NOT NULL,
  `product_name` varchar(250) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`notify_id`)
)
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 