<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS `suggestionbox` (
  `sbox_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(256) NOT NULL,
  `sbox_message` text NOT NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`sbox_id`)
) 
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
	 