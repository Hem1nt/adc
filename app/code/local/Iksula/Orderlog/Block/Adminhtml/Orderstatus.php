<?php


class Iksula_Orderlog_Block_Adminhtml_Orderstatus extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_orderstatus";
	$this->_blockGroup = "orderlog";
	$this->_headerText = Mage::helper("orderlog")->__("Orderstatus Manager");
	$this->_addButtonLabel = Mage::helper("orderlog")->__("Add New Item");
	parent::__construct();
	$this->_removeButton('add');
	}

}