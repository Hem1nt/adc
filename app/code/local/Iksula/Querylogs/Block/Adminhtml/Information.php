<?php


class Iksula_Querylogs_Block_Adminhtml_Information extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_information";
	$this->_blockGroup = "querylogs";
	$this->_headerText = Mage::helper("querylogs")->__("Information Manager");
	$this->_addButtonLabel = Mage::helper("querylogs")->__("Add New Item");
	parent::__construct();
	
	}

}