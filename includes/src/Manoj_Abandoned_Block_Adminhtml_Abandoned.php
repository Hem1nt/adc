<?php


class Manoj_Abandoned_Block_Adminhtml_Abandoned extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_abandoned";
	$this->_blockGroup = "abandoned";
	$this->_headerText = Mage::helper("abandoned")->__("Abandoned Manager");
	$this->_addButtonLabel = Mage::helper("abandoned")->__("Add New Item");
	parent::__construct();
	
	}

}