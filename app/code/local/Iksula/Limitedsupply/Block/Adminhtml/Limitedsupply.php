<?php


class Iksula_Limitedsupply_Block_Adminhtml_Limitedsupply extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_limitedsupply";
	$this->_blockGroup = "limitedsupply";
	$this->_headerText = Mage::helper("limitedsupply")->__("Limitedsupply Manager");
	$this->_addButtonLabel = Mage::helper("limitedsupply")->__("Add New Item");
	parent::__construct();
	
	}

}