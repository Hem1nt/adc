<?php

class Iksula_Customerinfo_Block_Adminhtml_Customerinfo_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("customerinfoGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("ASC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("customerinfo/customerinfo")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("customerinfo")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("name", array(
				"header" => Mage::helper("customerinfo")->__("name"),
				"index" => "name",
				));
				$this->addColumn("email", array(
				"header" => Mage::helper("customerinfo")->__("email"),
				"index" => "email",
				));
					$this->addColumn('dob', array(
						'header'    => Mage::helper('customerinfo')->__('dob'),
						'index'     => 'dob',
						'type'      => 'datetime',
					));
					$this->addColumn('anniversary', array(
						'header'    => Mage::helper('customerinfo')->__('anniversary'),
						'index'     => 'anniversary',
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
			$this->getMassactionBlock()->addItem('remove_customerinfo', array(
					 'label'=> Mage::helper('customerinfo')->__('Remove Customerinfo'),
					 'url'  => $this->getUrl('*/adminhtml_customerinfo/massRemove'),
					 'confirm' => Mage::helper('customerinfo')->__('Are you sure?')
				));
			return $this;
		}
			

}