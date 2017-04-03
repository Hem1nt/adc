<?php

class Iksula_Refillreminder_Block_Adminhtml_Orderreminder extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_orderreminder";
	$this->_blockGroup = "refillreminder";
	$this->_headerText = Mage::helper("refillreminder")->__("Orderreminder Manager");
	$this->_addButtonLabel = Mage::helper("refillreminder")->__("Add New Item");
	parent::__construct();
	
	}

}