
<?php 

Class Iksula_Overrides_Block_Adminhtml_Report_Product_Renderer_CategoryIds extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row){
		$categoryIds = $row->getCategoryIds();
		return implode(',',$categoryIds);
	}
}

?>
