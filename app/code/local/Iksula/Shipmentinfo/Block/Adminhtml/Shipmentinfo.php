<?php


class Iksula_Shipmentinfo_Block_Adminhtml_Shipmentinfo extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_shipmentinfo";
	$this->_blockGroup = "shipmentinfo";
	$this->_headerText = Mage::helper("shipmentinfo")->__("Shipmentinfo Manager");
	$this->_addButtonLabel = Mage::helper("shipmentinfo")->__("Add New Item");
	parent::__construct();
	
	}

}