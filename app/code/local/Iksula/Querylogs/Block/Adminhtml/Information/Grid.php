<?php

class Iksula_Querylogs_Block_Adminhtml_Information_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("informationGrid");
				$this->setDefaultSort("contact_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("querylogs/information")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("contact_id", array(
				"header" => Mage::helper("querylogs")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "contact_id",
				));
                
				$this->addColumn("email", array(
				"header" => Mage::helper("querylogs")->__("Email Id"),
				"index" => "email",
				));
				
				$this->addColumn("comment", array(
				"header" => Mage::helper("querylogs")->__("Comments"),
				"index" => "comment",	
				));
				
				$this->addColumn("querytype", array(
				"header" => Mage::helper("querylogs")->__("Query Type"),
				"index" => "querytype",
				));
				
				$this->addColumn('date_of_query', array(
				'header'    => Mage::helper('querylogs')->__('Date'),
				'index'     => 'date_of_query',
				'type'      => 'datetime',
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
			$this->setMassactionIdField('contact_id');
			$this->getMassactionBlock()->setFormFieldName('contact_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_information', array(
					 'label'=> Mage::helper('querylogs')->__('Remove Information'),
					 'url'  => $this->getUrl('*/adminhtml_information/massRemove'),
					 'confirm' => Mage::helper('querylogs')->__('Are you sure?')
				));
			return $this;
		}
			

}