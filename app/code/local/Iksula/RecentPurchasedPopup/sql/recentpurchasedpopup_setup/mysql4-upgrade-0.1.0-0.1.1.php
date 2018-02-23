<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
ALTER TABLE recentpurchasedpopup
ADD customer_id int; 
ALTER TABLE recentpurchasedpopup
ADD shipping_id int;
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();