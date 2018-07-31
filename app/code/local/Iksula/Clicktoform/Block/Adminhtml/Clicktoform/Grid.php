<?php

class Iksula_Clicktoform_Block_Adminhtml_Clicktoform_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	public function __construct()
	{
		parent::__construct();
		$this->setId("customeformid");
		$this->setDefaultSort("customeform_id");
		$this->setDefaultDir("DESC");
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel("clicktoform/clicktoform")->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	protected function _prepareColumns()
	{
		
	// Add the columns that should appear in the grid
	$this->addColumn("customeform_id", array(
				"header" => Mage::helper("clicktoform")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "customeform_id",
				));
		
		$this->addColumn('customer_name',
			array(
				'header'=> $this->__('Customer Name'),
				'index' => 'customer_name'
			)
		); 
		$this->addColumn('customer_email',
			array(
				'header'=> $this->__('Customer Email'),
				'index' => 'customer_email'
			)
		);
		$this->addColumn('customer_mobileno',
			array(
				'header'=> $this->__('Customer Contact Number'),
				'index' => 'customer_mobileno'
			)
		); 
		$this->addColumn('customer_time',
			array(
				'header'=> $this->__('Customer Time to call'),
				'index' => 'customer_time'
			)
		);
		$this->addColumn('customer_comment',
			array(
				'header'=> $this->__('Customer Comment'),
				'index' => 'customer_comment'
			)
		);  
		$this->addColumn("customer_calling_status", array(
				"header" => Mage::helper("clicktoform")->__("Status"),
				"index"  => "customer_calling_status",
				'type'   => 'options',
				'options' => array('0' => 'Disabled','1' => 'Enabled')
		));    
		$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
		$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

		return parent::_prepareColumns();
	}

	public function getRowUrl($row)
	{
		return $this->getUrl("*/*/edit", array("id" => $row->getId()));
	}


	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('customeform_id');
		$this->getMassactionBlock()->setFormFieldName('customeform_ids');
		$this->getMassactionBlock()->setUseSelectAll(true);
		$this->getMassactionBlock()->addItem('remove_customerinfo', array(
			'label'=> Mage::helper('clicktoform')->__('Remove Customer'),
			'url'  => $this->getUrl('*/adminhtml_clicktoform/massRemove'),
			'confirm' => Mage::helper('clicktoform')->__('Are you sure?')
		));
		return $this;
	}
	

}