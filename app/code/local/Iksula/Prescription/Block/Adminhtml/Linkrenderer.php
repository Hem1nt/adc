<?php
class Iksula_Prescription_Block_Adminhtml_Linkrenderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
	{
		$prescriptionLink = $row->getData('prescription');
		$link = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'media/Prescription/'.$prescriptionLink;
		if($prescriptionLink){
		$url = $this->getUrl("prescription/adminhtml_prescriptionbackend/DownloadFile/filename/".$prescriptionLink);
			return '<span style="color:red;"><a href="'.$link.'" target="blank">View</a></span>';
		}else{
			return '<span style="color:red;">NA</span>';
		}
	}
 
}
?>