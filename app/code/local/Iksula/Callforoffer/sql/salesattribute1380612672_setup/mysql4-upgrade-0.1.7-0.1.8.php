<?php
$installer = $this;
$installer->startSetup();
$installer->addAttribute("order", "paymentinfo_message", array("type"=>"varchar"));
$installer->endSetup();

