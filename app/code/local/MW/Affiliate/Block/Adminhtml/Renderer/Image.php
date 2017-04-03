<?php

class MW_Affiliate_Block_Adminhtml_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if (empty($row['image_name'])) return '';
    	//return '<img src="'. Mage::getBaseUrl('media').$row['image_name']. '" />';
    	//return '<img src="'. Mage::helper('catalog/image')->init($row, 'image_name')->resize(150)->__toString(). '" />';
    	return '<img src="'.Mage::helper('affiliate/image')->init($row['image_name'])->resize(60,60). '" />';
    }

}