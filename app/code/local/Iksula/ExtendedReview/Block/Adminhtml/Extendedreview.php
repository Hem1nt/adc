<?php


class Iksula_ExtendedReview_Block_Adminhtml_Extendedreview extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_extendedreview";
	$this->_blockGroup = "extendedreview";
	$this->_headerText = Mage::helper("extendedreview")->__("Extendedreview Manager");
	$this->_addButtonLabel = Mage::helper("extendedreview")->__("Add New Item");
	parent::__construct();
	// $this->_removeButton('add');
	}

}