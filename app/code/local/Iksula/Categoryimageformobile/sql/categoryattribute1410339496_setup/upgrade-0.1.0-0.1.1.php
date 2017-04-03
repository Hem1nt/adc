<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("catalog_category", "desktop_banner",  array(
    "type"     => "text",
    "backend"  => "catalog/category_attribute_backend_image",
    "frontend" => "",
    "label"    => "Best Selling Banner for Desktop",
    "input"    => "image",
    "class"    => "",
    "source"   => "",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    "visible"  => true,
    "required" => false,
    "user_defined"  => false,
    "default" => "",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,
    "visible_on_front"  => false,
    "unique"     => false,
    "note"       => "",
    "group"      => "General Information"
	));
$installer->endSetup();
	 