<?php
$installer = $this;
$installer->startSetup();

$installer->addAttribute("order", "order_payment_status", array("type"=>"varchar"));
$installer->addAttribute("order", "order_payment_type", array("type"=>"varchar"));

$installer->endSetup();