<?php
$installer = $this;
$installer->startSetup();

$installer->addAttribute("order", "physicianname", array("type"=>"varchar"));
$installer->addAttribute("order", "physiciantelephone", array("type"=>"varchar"));
$installer->addAttribute("order", "drug_allergies", array("type"=>"varchar"));
$installer->addAttribute("order", "current_medications", array("type"=>"varchar"));
$installer->addAttribute("order", "current_treatments", array("type"=>"varchar"));
$installer->addAttribute("order", "smoke", array("type"=>"varchar"));
$installer->addAttribute("order", "drink", array("type"=>"varchar"));
$installer->endSetup();
	 