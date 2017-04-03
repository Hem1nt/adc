<?php


class Iksula_Cashback_Block_Adminhtml_Cashback extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_cashback";
	$this->_blockGroup = "cashback";
	$this->_headerText = Mage::helper("cashback")->__("Cashback Manager");
	$this->_addButtonLabel = Mage::helper("cashback")->__("Add New Item");
	parent::__construct();
	
	}

}