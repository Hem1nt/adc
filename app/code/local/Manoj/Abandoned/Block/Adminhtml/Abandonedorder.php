<?php


class Manoj_Abandoned_Block_Adminhtml_Abandonedorder extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_abandonedorder";
	$this->_blockGroup = "abandoned";
	$this->_headerText = Mage::helper("abandoned")->__("Abandonedorder Manager");
	$this->_addButtonLabel = Mage::helper("abandoned")->__("Add New Item");
	parent::__construct();
	
	}

}