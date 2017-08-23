<?php
$installer = $this;
$installer->startSetup();


$installer->removeAttribute("catalog_category", "popular_banner");
$installer->removeAttribute("catalog_category", "desktop_banner");
$installer->endSetup();
	 