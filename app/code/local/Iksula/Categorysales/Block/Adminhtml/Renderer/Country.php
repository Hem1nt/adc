
<?php

Class Iksula_Categorysales_Block_Adminhtml_Renderer_Country extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row){
		$country_id = $row->getData('country_id');
		$country = Mage::getModel('directory/country')->loadByCode($country_id);
		$html = $country->getName();
		return $html;
	}
}

?>
