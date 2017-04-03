<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS `old_order` (
  `old_order_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `product_total` int(5) NOT NULL,
  `product_sku` text NOT NULL,
  `shipping_cost` varchar(255) NOT NULL,
  `total_cart_cost` varchar(255) NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`old_order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 