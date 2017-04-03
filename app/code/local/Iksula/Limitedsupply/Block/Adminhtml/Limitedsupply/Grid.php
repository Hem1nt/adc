<?php

class Iksula_Limitedsupply_Block_Adminhtml_Limitedsupply_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("limitedsupplyGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("limitedsupply/limitedsupply")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("limitedsupply")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));

				$this->addColumn("product_sku", array(
				"header" => Mage::helper("limitedsupply")->__("product_sku"),
				"index" => "product_sku",
				));
				$this->addColumn("product_name", array(
				"header" => Mage::helper("limitedsupply")->__("product_name"),
				"index" => "product_name",
				));
				// $this->addColumn('is_active', array(
				// 'header' => Mage::helper('limitedsupply')->__('is_active'),
				// 'index' => 'is_active',
				// 'type' => 'options',
				// 'options'=>Iksula_Limitedsupply_Block_Adminhtml_Limitedsupply_Grid::getOptionArray4(),
				// ));

				$this->addColumn("email", array(
				"header" => Mage::helper("limitedsupply")->__("email"),
				"index" => "email",
				));

				$this->addColumn("name", array(
				"header" => Mage::helper("limitedsupply")->__("name"),
				"index" => "name",
				));

				$this->addColumn("phone_no", array(
				"header" => Mage::helper("limitedsupply")->__("phone_no"),
				"index" => "phone_no",
				));

				$this->addColumn("quantity", array(
				"header" => Mage::helper("limitedsupply")->__("quantity"),
				"index" => "quantity",
				));

				$this->addColumn("comment", array(
				"header" => Mage::helper("limitedsupply")->__("comment"),
				"index" => "comment",
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
			$this->getMassactionBlock()->addItem('remove_limitedsupply', array(
					 'label'=> Mage::helper('limitedsupply')->__('Remove Limitedsupply'),
					 'url'  => $this->getUrl('*/adminhtml_limitedsupply/massRemove'),
					 'confirm' => Mage::helper('limitedsupply')->__('Are you sure?')
				));
			return $this;
		}

		static public function getOptionArray4()
		{
            $data_array=array();
			$data_array[0]='Yes';
			$data_array[1]='No';
            return($data_array);
		}
		static public function getValueArray4()
		{
            $data_array=array();
			foreach(Iksula_Limitedsupply_Block_Adminhtml_Limitedsupply_Grid::getOptionArray4() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);
			}
            return($data_array);

		}


}
