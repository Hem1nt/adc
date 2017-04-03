<?php

class Iksula_Backendfaq_Block_Adminhtml_Backendfaq_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("backendfaqGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("ASC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("backendfaq/backendfaq")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("backendfaq")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("question", array(
				"header" => Mage::helper("backendfaq")->__("Question"),
				"index" => "question",
				"width" => "350px",
				));
				$this->addColumn("answer", array(
				"header" => Mage::helper("backendfaq")->__("Answer"),
				"index" => "answer",
				));
				$this->addColumn("username", array(
				"header" => Mage::helper("backendfaq")->__("Username"),
				"index" => "username",
				));
				$this->addColumn("topic", array(
				"header" => Mage::helper("backendfaq")->__("Topic"),
				"index" => "topic",
				));
					$this->addColumn('date', array(
						'header'    => Mage::helper('backendfaq')->__('Date'),
						'index'     => 'date',
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
			$this->setMassactionIdField('id');
			$this->getMassactionBlock()->setFormFieldName('ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_backendfaq', array(
					 'label'=> Mage::helper('backendfaq')->__('Remove Backendfaq'),
					 'url'  => $this->getUrl('*/adminhtml_backendfaq/massRemove'),
					 'confirm' => Mage::helper('backendfaq')->__('Are you sure?')
				));
			return $this;
		}
			

}