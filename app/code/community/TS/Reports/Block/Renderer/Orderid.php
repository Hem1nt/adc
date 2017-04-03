<?php
class TS_Reports_Block_Renderer_Orderid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
	
	public function render(Varien_Object $row){
		$value =  $row->getData($this->getColumn()->getIndex());
		$url = Mage::helper('adminhtml')->getUrl('adminhtml/sales_order/view', 
			array('order_id' => $row->getData('order_id'))
		);
			
		return '<a href="'.$url.'">'.$value .'</a>';
	}
}
