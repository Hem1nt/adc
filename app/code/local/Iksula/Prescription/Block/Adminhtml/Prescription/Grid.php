<?php

class Iksula_Prescription_Block_Adminhtml_Prescription_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("prescriptionGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("prescription/prescription")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("prescription")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("order_id", array(
				"header" => Mage::helper("prescription")->__("Order Id"),
				"index" => "order_id",
				));
				$this->addColumn("customer_name", array(
				"header" => Mage::helper("prescription")->__("Customer Name"),
				"index" => "customer_name",
				));
				$this->addColumn("prescription_link", array(
				"header" => Mage::helper("prescription")->__("Download Prescription"),
				'renderer'  => 'Iksula_Prescription_Block_Adminhtml_Renderer',
				));
				$this->addColumn("prescription", array(
				"header" => Mage::helper("prescription")->__("View Prescription"),
				"renderer" => "Iksula_Prescription_Block_Adminhtml_Linkrenderer",
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
			$this->getMassactionBlock()->addItem('remove_prescription', array(
					 'label'=> Mage::helper('prescription')->__('Remove Prescription'),
					 'url'  => $this->getUrl('*/adminhtml_prescription/massRemove'),
					 'confirm' => Mage::helper('prescription')->__('Are you sure?')
				));
			return $this;
		}
			

}