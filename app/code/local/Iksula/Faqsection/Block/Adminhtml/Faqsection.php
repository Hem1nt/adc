<?php


class Iksula_Faqsection_Block_Adminhtml_Faqsection extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_faqsection";
	$this->_blockGroup = "faqsection";
	$this->_headerText = Mage::helper("faqsection")->__("Faqsection Manager");
	$this->_addButtonLabel = Mage::helper("faqsection")->__("Add New Item");
	parent::__construct();
	
	}

}