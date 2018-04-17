<?php

class Manoj_Abandoned_Block_Adminhtml_Abandoned_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("abandonedGrid");
				$this->setDefaultSort("abandoned_cart_id");
				$this->setDefaultDir("ASC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("abandoned/abandoned")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("abandoned_cart_id", array(
				"header" => Mage::helper("abandoned")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "abandoned_cart_id",
				));
                
				$this->addColumn("email_id", array(
				"header" => Mage::helper("abandoned")->__("email_id"),
				"index" => "email_id",
				));
				$this->addColumn("quote_id", array(
				"header" => Mage::helper("abandoned")->__("quote_id"),
				"index" => "quote_id",
				));
				$this->addColumn("capture_page", array(
				"header" => Mage::helper("abandoned")->__("abandoned_page_capture"),
				"index" => "abandoned_page_capture",
				));
						$this->addColumn('is_email_send', array(
						'header' => Mage::helper('abandoned')->__('is_email_send'),
						'index' => 'is_email_send',
						'type' => 'options',
						'options'=>Manoj_Abandoned_Block_Adminhtml_Abandoned_Grid::getOptionArray3(),				
						));
						
						$this->addColumn('is_purchase', array(
						'header' => Mage::helper('abandoned')->__('is_purchase'),
						'index' => 'is_purchase',
						'type' => 'options',
						'options'=>Manoj_Abandoned_Block_Adminhtml_Abandoned_Grid::getOptionArray4(),				
						));
						
					$this->addColumn('created_time', array(
						'header'    => Mage::helper('abandoned')->__('created_time'),
						'index'     => 'created_time',
						'type'      => 'datetime',
					));
					$this->addColumn('update_time', array(
						'header'    => Mage::helper('abandoned')->__('update_time'),
						'index'     => 'update_time',
						'type'      => 'datetime',
					));
				$this->addColumn("product_ids", array(
				"header" => Mage::helper("abandoned")->__("product_ids"),
				"index" => "product_ids",
				));
				$this->addColumn("subtotal", array(
				"header" => Mage::helper("abandoned")->__("subtotal"),
				"index" => "subtotal",
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
			$this->setMassactionIdField('abandoned_cart_id');
			$this->getMassactionBlock()->setFormFieldName('abandoned_cart_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_abandoned', array(
					 'label'=> Mage::helper('abandoned')->__('Remove Abandoned'),
					 'url'  => $this->getUrl('*/adminhtml_abandoned/massRemove'),
					 'confirm' => Mage::helper('abandoned')->__('Are you sure?')
				));
			return $this;
		}
			
		static public function getOptionArray3()
		{
            $data_array=array(); 
			$data_array[0]='No';
			$data_array[1]='Yes';
            return($data_array);
		}
		static public function getValueArray3()
		{
            $data_array=array();
			foreach(Manoj_Abandoned_Block_Adminhtml_Abandoned_Grid::getOptionArray3() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		static public function getOptionArray4()
		{
            $data_array=array(); 
			$data_array[0]='No';
			$data_array[1]='Yes';
            return($data_array);
		}
		static public function getValueArray4()
		{
            $data_array=array();
			foreach(Manoj_Abandoned_Block_Adminhtml_Abandoned_Grid::getOptionArray4() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		

}