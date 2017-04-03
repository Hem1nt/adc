<?php

class Iksula_Medical_Block_Adminhtml_Medical_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("medicalGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("ASC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("medical/medical")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("medical")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("orderid", array(
				"header" => Mage::helper("medical")->__("Order Id"),
				"index" => "orderid",
				));
				$this->addColumn("physicianname", array(
				"header" => Mage::helper("medical")->__("Physician Name"),
				"index" => "physicianname",
				));
				$this->addColumn("physiciantelephone", array(
				"header" => Mage::helper("medical")->__("Physician Telephone"),
				"index" => "physiciantelephone",
				));
				$this->addColumn("drugallergies", array(
				"header" => Mage::helper("medical")->__("Drug Allergies"),
				"index" => "drugallergies",
				));

				$this->addColumn("currentmedications", array(
				"header" => Mage::helper("medical")->__("Current Medications"),
				"index" => "currentmedications",
				));

				$this->addColumn("currenttreatments", array(
				"header" => Mage::helper("medical")->__("Current Treatments"),
				"index" => "currenttreatments",
				));
				$this->addColumn("smoke", array(
				"header" => Mage::helper("medical")->__("Smoke"),
				"index" => "smoke",
				));
				$this->addColumn("drink", array(
				"header" => Mage::helper("medical")->__("Drink"),
				"index" => "drink",
				));
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return '#';
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('id');
			$this->getMassactionBlock()->setFormFieldName('ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_medical', array(
					 'label'=> Mage::helper('medical')->__('Remove Medical'),
					 'url'  => $this->getUrl('*/adminhtml_medical/massRemove'),
					 'confirm' => Mage::helper('medical')->__('Are you sure?')
				));
			return $this;
		}
			

}