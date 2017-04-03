<?php


class Iksula_Refillreminder_Block_Adminhtml_Refillreminder extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_refillreminder";
	$this->_blockGroup = "refillreminder";
	$this->_headerText = Mage::helper("refillreminder")->__("Refillreminder Manager");
	$this->_addButtonLabel = Mage::helper("refillreminder")->__("Add New Item");
	parent::__construct();
	
	}

}