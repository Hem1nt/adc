<?php
class Iksula_Backendfaq_Block_Adminhtml_Backendfaq extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_backendfaq";
	$this->_blockGroup = "backendfaq";
	$this->_headerText = Mage::helper("backendfaq")->__("Backendfaq Manager");
	$this->_addButtonLabel = Mage::helper("backendfaq")->__("Add New Item");
	parent::__construct();
	
	}

}