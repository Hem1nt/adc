<?php
$installer = $this;
$installer->startSetup();
$installer->addAttribute("order", "agent_name", array("type"=>"varchar"));
$installer->endSetup();
	 