<?php 
class Iksula_Echecksteps_Block_Adminhtml_Echecksteps_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{

public function render(Varien_Object $row)
{

	return '<img height="100px" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).DS.$row->getData('image_name').'" />';	

}

}