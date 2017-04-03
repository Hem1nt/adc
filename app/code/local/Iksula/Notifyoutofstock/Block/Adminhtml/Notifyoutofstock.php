<?php


class Iksula_Notifyoutofstock_Block_Adminhtml_Notifyoutofstock extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_notifyoutofstock";
	$this->_blockGroup = "notifyoutofstock";
	$this->_headerText = Mage::helper("notifyoutofstock")->__("Notifyoutofstock Manager");
	$this->_addButtonLabel = Mage::helper("notifyoutofstock")->__("Add New Item");
	parent::__construct();
	
	}

}