<?php


class Iksula_Echecksteps_Block_Adminhtml_Echecksteps extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_echecksteps";
	$this->_blockGroup = "echecksteps";
	$this->_headerText = Mage::helper("echecksteps")->__("Echecksteps Manager");
	$this->_addButtonLabel = Mage::helper("echecksteps")->__("Add New Item");
	parent::__construct();
	
	}

}