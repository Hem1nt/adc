<?php


class Iksula_Medical_Block_Adminhtml_Medical extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_medical";
	$this->_blockGroup = "medical";
	$this->_headerText = Mage::helper("medical")->__("Medical Manager");
	$this->_addButtonLabel = Mage::helper("medical")->__("Add New Item");
	parent::__construct();
	
	}

}