
<?php

Class Iksula_Reports_Block_Adminhtml_Renderer_Gender extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row){
		$gender = $row->getData('customer_gender');
		$genderLabel = $this->getOptionText($gender);

		return $genderLabel;
	}

	public function getOptionText($code) {
		$text = Mage::getResourceSingleton('customer/customer')
			    ->getAttribute('gender')
			    ->getSource()->getOptionText($code);
		return $text;
	}
}

?>
