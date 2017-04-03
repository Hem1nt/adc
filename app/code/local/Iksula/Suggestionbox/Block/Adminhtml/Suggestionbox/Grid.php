<?php

class Iksula_Suggestionbox_Block_Adminhtml_Suggestionbox_Grid extends Mage_Adminhtml_Block_Widget_Grid{

	public function __construct(){
		parent::__construct();
		$this->setId("suggestionboxGrid");
		$this->setDefaultSort("sbox_id");
		$this->setDefaultDir("ASC");
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()	{
		$collection = Mage::getModel("suggestionbox/suggestionbox")->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	protected function _prepareColumns(){
		$this->addColumn("sbox_id", array(
		"header" => Mage::helper("suggestionbox")->__("ID"),
		"align" =>"right",
		"width" => "50px",
	    "type" => "number",
		"index" => "sbox_id",
		));

		$this->addColumn("sbox_name", array(
		"header" => Mage::helper("suggestionbox")->__("Name"),
		"index" => "sbox_name",
		));

		$this->addColumn("email", array(
		"header" => Mage::helper("suggestionbox")->__("Email"),
		"index" => "email",
		));

		$this->addColumn("sbox_message", array(
		"header" => Mage::helper("suggestionbox")->__("Suggestion"),
		"index" => "sbox_message",
		));

		$this->addColumn("created_date", array(
		"header" => Mage::helper("suggestionbox")->__("Created at"),
		"index" => "created_date",
		));	
		$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
		$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));
		return parent::_prepareColumns();
	}

}