<?php
$installer = $this;
$installer->startSetup();
$installer->addAttribute("order", "customer_order_increment_id", array("type"=>"varchar"));
$installer->endSetup();

