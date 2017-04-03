<?php

class Iksula_Orderlog_Block_Adminhtml_Ordertracking_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("ordertrackingGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("orderlog/ordertracking")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("orderlog")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("order_id", array(
				"header" => Mage::helper("orderlog")->__("Order Id"),
				"index" => "order_id",
				));

				$this->addColumn("track_id", array(
				"header" => Mage::helper("orderlog")->__("Tracking Id"),
				"index" => "track_id",
				));

				$this->addColumn("user", array(
				"header" => Mage::helper("orderlog")->__("User"),
				"index" => "user",
				));

				$this->addColumn("shipping_part", array(
				"header" => Mage::helper("orderlog")->__("Shipping Part"),
				"index" => "shipping_part",
				));

				$this->addColumn('date', array(
					'header'    => Mage::helper('orderlog')->__('Date'),
					'index'     => 'date',
					'type'      => 'datetime',
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
			$this->getMassactionBlock()->addItem('remove_ordertracking', array(
					 'label'=> Mage::helper('orderlog')->__('Remove Ordertracking'),
					 'url'  => $this->getUrl('*/adminhtml_ordertracking/massRemove'),
					 'confirm' => Mage::helper('orderlog')->__('Are you sure?')
				));
			return $this;
		}
			

}