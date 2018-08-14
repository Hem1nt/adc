<?php
class Iksula_Clicktoform_Block_Adminhtml_Clicktoform extends Mage_Adminhtml_Block_Widget_Grid_Container {
    public function __construct()
    {
    	
        $this->_controller = "adminhtml_clicktoform";
		$this->_blockGroup = "clicktoform";
		$this->_headerText = Mage::helper("clicktoform")->__("Customer Click To Call Manager");
		$this->_addButtonLabel = Mage::helper("clicktoform")->__("Add New Item");
		parent::__construct();
    }
}