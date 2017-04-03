<?php
$installer = $this;
$installer->startSetup();
$installer->addAttribute("order", "fromdate_message", array("type"=>"date"));
$installer->addAttribute("order", "todate_message", array("type"=>"date"));
$installer->endSetup();

