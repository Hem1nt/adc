<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
ALTER TABLE salesrule
ADD in_promo_page INT;
SQLTEXT;

$installer->run($sql);
$installer->endSetup();
	 