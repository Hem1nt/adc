<?php

class Manoj_Abandoned_Block_Adminhtml_Abandonedorder_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("abandonedorderGrid");
				$this->setDefaultSort("abandoned_order_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("abandoned/abandonedorder")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("abandoned_order_id", array(
				"header" => Mage::helper("abandoned")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "abandoned_order_id",
				));

				$this->addColumn("order_number", array(
				"header" => Mage::helper("abandoned")->__("order_number"),
				"index" => "order_number",
				));

				$this->addColumn("email_id", array(
				"header" => Mage::helper("abandoned")->__("email_id"),
				"index" => "email_id",
				));
				
				$this->addColumn('created_time', array(
						'header'    => Mage::helper('abandoned')->__('created_time'),
						'index'     => 'created_time',
						'type'      => 'datetime',
					));
				/*$this->addColumn('update_time', array(
						'header'    => Mage::helper('abandoned')->__('update_time'),
						'index'     => 'update_time',
						'type'      => 'datetime',
					));*/
                
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
			$this->setMassactionIdField('abandoned_order_id');
			$this->getMassactionBlock()->setFormFieldName('abandoned_order_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_abandonedorder', array(
					 'label'=> Mage::helper('abandoned')->__('Remove Abandonedorder'),
					 'url'  => $this->getUrl('*/adminhtml_abandonedorder/massRemove'),
					 'confirm' => Mage::helper('abandoned')->__('Are you sure?')
				));
			return $this;
		}
			

}