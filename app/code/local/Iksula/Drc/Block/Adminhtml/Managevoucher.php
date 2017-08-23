<?php


class Iksula_Drc_Block_Adminhtml_Managevoucher extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_managevoucher";
	$this->_blockGroup = "drc";
	$this->_headerText = Mage::helper("drc")->__("Managevoucher Manager");
	$this->_addButtonLabel = Mage::helper("drc")->__("Add New Item");
	parent::__construct();
	
	}

}