<?php
class TS_Reports_Block_Renderer_Productid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	
	public function render(Varien_Object $row){
		$value =  $row->getData($this->getColumn()->getIndex());
		$url = Mage::helper('adminhtml')->getUrl('adminhtml/catalog_product/edit', 
			array('id' => $row->getData('product_id'))
		);
		return '<a href="'.$url.'">'.$value .'</a>';
	}
}
