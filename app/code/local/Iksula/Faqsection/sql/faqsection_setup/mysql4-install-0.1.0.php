<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT

CREATE TABLE IF NOT EXISTS `faq_sections_type` (
  `sections_typeid` int(11) NOT NULL AUTO_INCREMENT,
  `type_title` varchar(1000) NOT NULL,
  PRIMARY KEY (`sections_typeid`)
);

CREATE TABLE IF NOT EXISTS `faq_questions` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  sections_id int(11),
  question varchar(1000),
  answer text,
  PRIMARY KEY (`question_id`)
);

SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 