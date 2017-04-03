<?php

class Iksula_Faqsection_Block_Adminhtml_Faqsection_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("faqsectionGrid");
				$this->setDefaultSort("sections_typeid");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("faqsection/faqsection")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("sections_typeid", array(
				"header" => Mage::helper("faqsection")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "sections_typeid",
				));
                
				$this->addColumn("type_title", array(
				"header" => Mage::helper("faqsection")->__("Type Titile"),
				"index" => "type_title",
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
			$this->setMassactionIdField('sections_typeid');
			$this->getMassactionBlock()->setFormFieldName('sections_typeids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_faqsection', array(
					 'label'=> Mage::helper('faqsection')->__('Remove Faqsection'),
					 'url'  => $this->getUrl('*/adminhtml_faqsection/massRemove'),
					 'confirm' => Mage::helper('faqsection')->__('Are you sure?')
				));
			return $this;
		}
			

}