<?php

class Iksula_Cashback_Block_Adminhtml_Cashback_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("cashbackGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("cashback/cashback")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("cashback")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("user_email", array(
				"header" => Mage::helper("cashback")->__("Email"),
				"index" => "user_email",
				));
				$this->addColumn("coupon_code", array(
				"header" => Mage::helper("cashback")->__("Coupon"),
				"index" => "coupon_code",
				));
				$this->addColumn("amount", array(
				"header" => Mage::helper("cashback")->__("Amount"),
				"index" => "amount",
				));
				$this->addColumn("use", array(
				"header" => Mage::helper("cashback")->__("Uses"),
				"index" => "use",
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
			$this->getMassactionBlock()->addItem('remove_cashback', array(
					 'label'=> Mage::helper('cashback')->__('Remove Cashback'),
					 'url'  => $this->getUrl('*/adminhtml_cashback/massRemove'),
					 'confirm' => Mage::helper('cashback')->__('Are you sure?')
				));
			return $this;
		}
			

}