<?php
$installer = $this;
$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('suggestionbox')} ADD COLUMN `sbox_name` varchar(255) NULL AFTER `sbox_message`");
$installer->endSetup();