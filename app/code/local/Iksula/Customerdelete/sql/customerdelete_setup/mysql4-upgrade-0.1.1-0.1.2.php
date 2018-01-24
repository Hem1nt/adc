<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
ALTER TABLE customer_entity
ADD customer_behavior varchar(300); 




SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();