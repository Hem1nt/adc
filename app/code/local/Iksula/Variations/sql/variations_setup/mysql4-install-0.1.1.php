<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS `variations` (
  `variations_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_sku` varchar(100) NOT NULL,
  `variations_strength` varchar(100) NOT NULL,
  `variations_url` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`variations_id`)
)
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 