<?php
$installer = $this;
$installer->startSetup();
$installer->addAttribute("order", "order_placed_country", array("type"=>"varchar"));
$installer->endSetup();

