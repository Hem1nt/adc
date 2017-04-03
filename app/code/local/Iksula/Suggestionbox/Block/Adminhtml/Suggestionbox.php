<?php

class Iksula_Suggestionbox_Block_Adminhtml_Suggestionbox extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()	{
	$this->_controller = "adminhtml_suggestionbox";
	$this->_blockGroup = "suggestionbox";
	$this->_headerText = Mage::helper("suggestionbox")->__("Suggestionbox Manager");
	parent::__construct();
	$this->_removeButton("add");	
	}

}