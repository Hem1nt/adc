<?php
class Iksula_Prescription_Block_Adminhtml_Renderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
	{
		$prescriptionLink = $row->getData('prescription');
		if($prescriptionLink){
		$url = $this->getUrl("prescription/adminhtml_prescriptionbackend/DownloadFile/filename/".$prescriptionLink);
			return '<span style="color:red;"><a href="'.$url.'">Download</a></span>';
		}else{
			return '<span style="color:red;">NA</span>';
		}
	}
 
}
?>