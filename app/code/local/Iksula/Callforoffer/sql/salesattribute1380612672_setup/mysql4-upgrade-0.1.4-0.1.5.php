<?php
$installer = $this;
$installer->startSetup();

$installer->addAttribute("order", "custom_charges", array("type"=>"float"));
$installer->addAttribute("order", "custom_charges_message", array("type"=>"varchar"));
$installer->addAttribute("order", "custom_grand_total", array("type"=>"float"));
$installer->addAttribute("order", "custom_base_grand_total", array("type"=>"float"));
$installer->endSetup();
	 