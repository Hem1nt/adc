<?php


class Iksula_Customerdelete_Block_Adminhtml_Customerdelete extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_customerdelete";
	$this->_blockGroup = "customerdelete";
	$this->_headerText = Mage::helper("customerdelete")->__("Customerdelete Manager");
	$this->_addButtonLabel = Mage::helper("customerdelete")->__("Add New Item");
	parent::__construct();
	
	}

}