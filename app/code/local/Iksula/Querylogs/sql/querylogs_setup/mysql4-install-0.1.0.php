<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS `querylogs` (
  `contact_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(256) DEFAULT NULL,
  `querytype` varchar(1000) DEFAULT NULL,
  `productname` varchar(1000) DEFAULT NULL,
  `timetocall` varchar(100) DEFAULT NULL,
  `timezone` varchar(100) DEFAULT NULL,
  `telephone` varchar(100) DEFAULT NULL,
  `ordernumber` varchar(100) DEFAULT NULL,
  `comment` text,
  `date_of_query` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`contact_id`)
)
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 