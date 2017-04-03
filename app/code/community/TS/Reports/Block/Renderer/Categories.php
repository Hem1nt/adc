<?php

class TS_Reports_Block_Renderer_Categories extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options {
	
	public function render(Varien_Object $row){
		$results = array();
		$unknowns = array();
		$categoryIds = Mage::helper('ts_reports')->desanitize($row->getData($this->getColumn()->getIndex()));
		$categories = Mage::getSingleton('core/session')->getTSReportsCategories();
		
		foreach($categoryIds as $categoryId){
			if(isset($categories[$categoryId]) && $cat = $categories[$categoryId]) $results[] = $cat['name'] .' ('. Mage::helper('ts_reports')->__('ID') .': '.$categoryId.')';
			else $unknowns[] = Mage::helper('ts_reports')->__('Uknown ID:') .' '.$categoryId;
		}
		sort($results, SORT_STRING);
		return implode('<br />', array_merge($results,$unknowns));
	}
}
