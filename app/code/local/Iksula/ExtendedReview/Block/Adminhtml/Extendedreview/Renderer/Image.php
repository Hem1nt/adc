<?php 
class Iksula_Extendedreview_Block_Adminhtml_Extendedreview_Renderer_Comment extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{

public function render(Varien_Object $row)
{

     echo "render";
	return '<img height="100px" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).DS.$row->getData('image_name').'" />';	

}

}