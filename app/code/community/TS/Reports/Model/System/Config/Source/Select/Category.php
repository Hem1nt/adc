<?php

class TS_Reports_Model_System_Config_Source_Select_Category {

    public function toOptionArray(){
		$category_collection = Mage::getModel('catalog/category')->getTreeModel()->load()->getCollection();		
		foreach($category_collection as $key => $category){	
			$parent_category_count = count(explode('/',$category->getPath()));
			$categories[] = array(
				'label' => ($parent_category_count > 1? str_repeat('|----', $parent_category_count-2).'|- ':''). Mage::helper('ts_reports')->__($category->getName()),
				'value' => $category->getId()
			);
		}
		return $categories;
	}
	
}