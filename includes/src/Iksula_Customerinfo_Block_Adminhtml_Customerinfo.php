<?php


class Iksula_Customerinfo_Block_Adminhtml_Customerinfo extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_customerinfo";
	$this->_blockGroup = "customerinfo";
	$this->_headerText = Mage::helper("customerinfo")->__("Customerinfo Manager");
	$this->_addButtonLabel = Mage::helper("customerinfo")->__("Add New Item");
	parent::__construct();
	
	}

}