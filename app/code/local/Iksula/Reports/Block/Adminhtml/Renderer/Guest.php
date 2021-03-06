
<?php

Class Iksula_Reports_Block_Adminhtml_Renderer_Guest extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row){
		$from_date = $row->getData('from_date');
		$to_date = $row->getData('to_date');
		$usertype = 'guest';
		$Url = Mage::helper("adminhtml")->getUrl("*/*/view/",array("from_date"=>$from_date,"to_date"=>$to_date,"usertype"=>$usertype));
		$guestUsercount = $row->getData('guest');
		$html = '<a href="'.$Url.'">'.$guestUsercount.'</a>';
		return $html;
	}
}

?>
