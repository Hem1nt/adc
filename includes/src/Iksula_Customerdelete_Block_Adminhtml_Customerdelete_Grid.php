<?php

class Iksula_Customerdelete_Block_Adminhtml_Customerdelete_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("customerdeleteGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("ASC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("customerdelete/customerdelete")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("customerdelete")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("entity_id", array(
				"header" => Mage::helper("customerdelete")->__("Entity Id"),
				"index" => "entity_id",
				));
				$this->addColumn("email", array(
				"header" => Mage::helper("customerdelete")->__("Email"),
				"index" => "email",
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
			$this->getMassactionBlock()->addItem('remove_customerdelete', array(
					 'label'=> Mage::helper('customerdelete')->__('Remove Customerdelete'),
					 'url'  => $this->getUrl('*/adminhtml_customerdelete/massRemove'),
					 'confirm' => Mage::helper('customerdelete')->__('Are you sure?')
				));
			return $this;
		}
			

}