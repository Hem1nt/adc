<?php


class Iksula_Prescription_Block_Adminhtml_Prescription extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_prescription";
	$this->_blockGroup = "prescription";
	$this->_headerText = Mage::helper("prescription")->__("Prescription Manager");
	$this->_addButtonLabel = Mage::helper("prescription")->__("Add New Item");
	parent::__construct();
	
	}

}