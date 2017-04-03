<?php


class Iksula_Birthday_Block_Adminhtml_Birthday extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_birthday";
	$this->_blockGroup = "birthday";
	$this->_headerText = Mage::helper("birthday")->__("Birthday Manager");
	$this->_addButtonLabel = Mage::helper("birthday")->__("Add New Item");
	parent::__construct();
	
	}

}