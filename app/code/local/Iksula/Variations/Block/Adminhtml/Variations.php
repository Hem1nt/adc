<?php


class Iksula_Variations_Block_Adminhtml_Variations extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_variations";
	$this->_blockGroup = "variations";
	$this->_headerText = Mage::helper("variations")->__("Variations Manager");
	$this->_addButtonLabel = Mage::helper("variations")->__("Add New Item");
	parent::__construct();
	
	}

}