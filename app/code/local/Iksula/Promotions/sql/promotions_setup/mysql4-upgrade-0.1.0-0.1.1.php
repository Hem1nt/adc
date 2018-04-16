<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
ALTER TABLE salesrule
ADD sortorder INT;
SQLTEXT;

$installer->run($sql);
$installer->endSetup();
	 