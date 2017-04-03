<?php

class Iksula_Notifyoutofstock_Block_Adminhtml_Notifyoutofstock_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("notifyoutofstockGrid");
				$this->setDefaultSort("notify_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("notifyoutofstock/notifyoutofstock")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("notify_id", array(
				"header" => Mage::helper("notifyoutofstock")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "notify_id",
				));
                
				$this->addColumn("product_id", array(
				"header" => Mage::helper("notifyoutofstock")->__("Product Id"),
				"index" => "product_id",
				));

				$this->addColumn("product_sku", array(
				"header" => Mage::helper("notifyoutofstock")->__("Product SKU"),
				"index" => "product_sku",
				));

				$this->addColumn("product_name", array(
				"header" => Mage::helper("notifyoutofstock")->__("Product Name"),
				"index" => "product_name",
				));

				$this->addColumn("count", array(
				"header" => Mage::helper("notifyoutofstock")->__("Count"),
				"index" => "count",
				));

				$this->addColumn('date', array(
					'header'    => Mage::helper('notifyoutofstock')->__('Date'),
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
			$this->setMassactionIdField('notify_id');
			$this->getMassactionBlock()->setFormFieldName('notify_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_notifyoutofstock', array(
					 'label'=> Mage::helper('notifyoutofstock')->__('Remove Notifyoutofstock'),
					 'url'  => $this->getUrl('*/adminhtml_notifyoutofstock/massRemove'),
					 'confirm' => Mage::helper('notifyoutofstock')->__('Are you sure?')
				));
			return $this;
		}
			

}