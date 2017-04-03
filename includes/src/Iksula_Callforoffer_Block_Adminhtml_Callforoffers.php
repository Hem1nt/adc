<?php


class Iksula_Callforoffer_Block_Adminhtml_Callforoffers extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_callforoffers";
	$this->_blockGroup = "callforoffer";
	$this->_headerText = Mage::helper("callforoffer")->__("Callforoffers Manager");
	$this->_addButtonLabel = Mage::helper("callforoffer")->__("Add New Item");
	parent::__construct();
	
	}

}