<?php


class Iksula_Trustedcompany_Block_Adminhtml_Reviews extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_reviews";
	$this->_blockGroup = "trustedcompany";
	$this->_headerText = Mage::helper("trustedcompany")->__("Reviews Manager");
	$this->_addButtonLabel = Mage::helper("trustedcompany")->__("Add New Item");
	parent::__construct();
	
	}

}