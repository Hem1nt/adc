
<?php

Class Iksula_Categorysales_Block_Adminhtml_Renderer_Customer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row){

		// echo "<pre>";  print('here'); print_r($row->getData('customer_email'));  exit;
		$customer_email = $row->getData('customer_email');
		// $firstname = $row->getData('customer_firstname');
		$Url = Mage::helper("adminhtml")->getUrl("*/*/view/",array("email"=>$customer_email));
		$html = '<a href="'.$Url.'">'.$customer_email.'</a>';
		return $html;
	}
}

?>
