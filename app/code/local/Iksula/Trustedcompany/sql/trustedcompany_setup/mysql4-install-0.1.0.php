<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS `trustedcompany_review` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `review_made_by` varchar(200) NOT NULL,
  `subject` varchar(1000) NOT NULL,
  `body` varchar(1000) NOT NULL,
  `rating` int(11) NOT NULL,
  `reviewer` varchar(1000) NOT NULL,
  `date` datetime NOT NULL,
  `extra_data` text NOT NULL,
  PRIMARY KEY (`review_id`)
)
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 