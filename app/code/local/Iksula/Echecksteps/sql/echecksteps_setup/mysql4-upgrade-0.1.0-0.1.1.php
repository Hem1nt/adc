<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
	ALTER TABLE  `echeck_working_steps` ADD  `description` TEXT NOT NULL AFTER  `steps_order` ;
SQLTEXT;

$installer->run($sql);

$installer->endSetup();	 