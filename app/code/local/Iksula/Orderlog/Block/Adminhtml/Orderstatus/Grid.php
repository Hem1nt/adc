<?php

class Iksula_Orderlog_Block_Adminhtml_Orderstatus_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("orderstatusGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("orderlog/orderstatus")->getCollection();
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
				$this->addColumn("status", array(
				"header" => Mage::helper("orderlog")->__("Status"),
				"index" => "status",
				));
				$this->addColumn("user", array(
				"header" => Mage::helper("orderlog")->__("User"),
				"index" => "user",
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


		
		// protected function _prepareMassaction()
		// {
		// 	$this->setMassactionIdField('id');
		// 	$this->getMassactionBlock()->setFormFieldName('ids');
		// 	$this->getMassactionBlock()->setUseSelectAll(true);
		// 	$this->getMassactionBlock()->addItem('remove_orderstatus', array(
		// 			 'label'=> Mage::helper('orderlog')->__('Remove Orderstatus'),
		// 			 'url'  => $this->getUrl('*/adminhtml_orderstatus/massRemove'),
		// 			 'confirm' => Mage::helper('orderlog')->__('Are you sure?')
		// 		));
		// 	return $this;
		// }
			

}