<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
ALTER TABLE extended_review
ADD reviewer_name varchar(300); 
SQLTEXT;
$installer->run($sql);
$installer->endSetup();

$installer->startSetup();
$sql=<<<SQLTEXT
ALTER TABLE extended_review
ADD product_name varchar(300); 
SQLTEXT;
$installer->run($sql);
$installer->endSetup();