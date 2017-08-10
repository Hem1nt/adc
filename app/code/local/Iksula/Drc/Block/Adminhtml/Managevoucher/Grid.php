<?php

class Iksula_Drc_Block_Adminhtml_Managevoucher_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("managevoucherGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("drc/managevoucher")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("drc")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("requested_by", array(
				"header" => Mage::helper("drc")->__("Voucher Requested By"),
				"index" => "requested_by",
				));
				$this->addColumn("email", array(
				"header" => Mage::helper("drc")->__("Voucher Requested For"),
				"index" => "email",
				));
				$this->addColumn("created_at", array(
				"header" => Mage::helper("drc")->__("Created At"),
				"index" => "created_at",
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
			$this->getMassactionBlock()->addItem('remove_managevoucher', array(
					 'label'=> Mage::helper('drc')->__('Remove Managevoucher'),
					 'url'  => $this->getUrl('*/adminhtml_managevoucher/massRemove'),
					 'confirm' => Mage::helper('drc')->__('Are you sure?')
				));
			return $this;
		}
			

}