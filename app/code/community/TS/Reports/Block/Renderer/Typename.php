<?php
class TS_Reports_Block_Renderer_Typename extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options {
	
	public function render(Varien_Object $row){
		$name = parent::render($row);
		if($row->getData($this->getColumn()->getIndex()) == TS_Reports_Model_Types::UNKNOWN) return '<i>'.$name.'</i>';
		return $name;
	}
}
