<?php

class Iksula_Callforoffer_Block_Adminhtml_Callforoffers_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("callforoffersGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("ASC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("callforoffer/callforoffers")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("callforoffer")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("cust_name", array(
				"header" => Mage::helper("callforoffer")->__("Customer Name"),
				"index" => "cust_name",
				));
				$this->addColumn("customerid", array(
				"header" => Mage::helper("callforoffer")->__("Customer Id"),
				"index" => "customerid",
				));
				$this->addColumn("customertelephoneno", array(
				"header" => Mage::helper("callforoffer")->__("Customer Telephone No"),
				"index" => "customertelephoneno",
				));
						$this->addColumn('status', array(
						'header' => Mage::helper('callforoffer')->__('Status'),
						'index' => 'status',
						'type' => 'options',
						'options'=>Iksula_Callforoffer_Block_Adminhtml_Callforoffers_Grid::getOptionArray4(),				
						));
						
						$this->addColumn('customertype', array(
						'header' => Mage::helper('callforoffer')->__('Customer Type'),
						'index' => 'customertype',
						'type' => 'options',
						'options'=>Iksula_Callforoffer_Block_Adminhtml_Callforoffers_Grid::getOptionArray5(),				
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
			$this->getMassactionBlock()->addItem('remove_callforoffers', array(
					 'label'=> Mage::helper('callforoffer')->__('Remove Callforoffers'),
					 'url'  => $this->getUrl('*/adminhtml_callforoffers/massRemove'),
					 'confirm' => Mage::helper('callforoffer')->__('Are you sure?')
				));
			return $this;
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
			foreach(Iksula_Callforoffer_Block_Adminhtml_Callforoffers_Grid::getOptionArray4() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		static public function getOptionArray5()
		{
            $data_array=array(); 
			$data_array[0]='Customer';
			$data_array[1]='Guest';
            return($data_array);
		}
		static public function getValueArray5()
		{
            $data_array=array();
			foreach(Iksula_Callforoffer_Block_Adminhtml_Callforoffers_Grid::getOptionArray5() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		

}