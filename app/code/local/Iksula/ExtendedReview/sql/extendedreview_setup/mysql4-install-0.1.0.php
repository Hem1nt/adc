<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT

CREATE TABLE IF NOT EXISTS `extended_review` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `review_id` int(10) unsigned NOT NULL,
  `comment_id` int(10) unsigned,
  `customer_id` int(10) unsigned NOT NULL,
  `approved_by` int(10) unsigned,
  `comment` text NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
	 