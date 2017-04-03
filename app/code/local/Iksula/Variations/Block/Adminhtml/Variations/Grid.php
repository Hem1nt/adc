<?php

class Iksula_Variations_Block_Adminhtml_Variations_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("variationsGrid");
				$this->setDefaultSort("variations_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("variations/variations")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("variations_id", array(
				"header" => Mage::helper("variations")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "variations_id",
				));
                
				$this->addColumn("product_sku", array(
				"header" => Mage::helper("variations")->__("Sku"),
				"index" => "product_sku",
				));
				$this->addColumn("variations_strength", array(
				"header" => Mage::helper("variations")->__("Strength"),
				"index" => "variations_strength",
				));
				$this->addColumn("variations_url", array(
				"header" => Mage::helper("variations")->__("Product Url"),
				"index" => "variations_url",
				));
				$this->addColumn("sort_order", array(
				"header" => Mage::helper("variations")->__("Sort Order"),
				"index" => "sort_order",
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
			$this->setMassactionIdField('variations_id');
			$this->getMassactionBlock()->setFormFieldName('variations_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_variations', array(
					 'label'=> Mage::helper('variations')->__('Remove Variations'),
					 'url'  => $this->getUrl('*/adminhtml_variations/massRemove'),
					 'confirm' => Mage::helper('variations')->__('Are you sure?')
				));
			return $this;
		}
			

}