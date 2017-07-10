<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE  `extended_review` CHANGE  `approved_by`  `approved_by` VARCHAR( 255 ) NULL DEFAULT NULL
	");
$installer->endSetup();