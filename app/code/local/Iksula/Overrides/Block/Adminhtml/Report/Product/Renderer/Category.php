
<?php 

Class Iksula_Overrides_Block_Adminhtml_Report_Product_Renderer_Category extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row){
		$categoryIds = $row->getCategoryIds();
		// $mediaurl=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		// $value = $row->getData($this->getColumn()->getIndex());
		foreach ($categoryIds as $category) {
			$category = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($category);
			$categoryName[] = $category->getName();
		}
		return implode(',',$categoryName);
	}
}

?>
