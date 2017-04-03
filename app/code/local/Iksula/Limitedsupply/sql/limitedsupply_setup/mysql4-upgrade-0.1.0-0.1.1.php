<?php
$installer = $this;
$installer->startSetup();
$installer->run("
  ALTER TABLE {$this->getTable('limitedsupply_info')}
  ADD COLUMN `name` VARCHAR(45) NOT NULL,
  ADD COLUMN `phone_no` INT(45) NOT NULL,
  ADD COLUMN `quantity` INT(45) NOT NULL;
  ");
$installer->endSetup();
