<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS `medical_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderid` varchar(100) DEFAULT NULL,
  `physicianname` varchar(1000) DEFAULT NULL,  `physiciantelephone` varchar(1000) DEFAULT NULL,  `drugallergies` varchar(1000) DEFAULT NULL,
  `currentmedications` varchar(1000) DEFAULT NULL,
  `currenttreatments` varchar(1000) DEFAULT NULL,  `smoke` varchar(100) DEFAULT NULL,  `drink` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
)
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 