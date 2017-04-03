<?php

class Iksula_Orderlog_Block_Adminhtml_Usertimelog_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("usertimelogGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
			$collection = Mage::getModel("orderlog/usertimelog")->getCollection();
			$this->setCollection($collection);
			parent::_prepareCollection();
			foreach ($collection as $item) {
				if($item->getOutTime() != "0000-00-00 00:00:00")
					$date = strtotime($item->getOutTime()) - strtotime($item->getEntryTime());
	           	else{
	           		$item->out_time = "";
	           		$date =strtotime(now())-strtotime($item->getEntryTime());
	           	}
	           	$item->totaltime = $date." seconds";
	        }				
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

			$this->addColumn("user_name", array(
			"header" => Mage::helper("orderlog")->__("User Name"),
			"index" => "user_name",
			));

			$this->addColumn("page_name", array(
			"header" => Mage::helper("orderlog")->__("Page Name"),
			"index" => "page_name",
			));

			// $this->addColumn('date', array(
			// 	'header'    => Mage::helper('orderlog')->__('Date'),
			// 	'index'     => 'date',
			// 	'type'      => 'datetime',
			// ));

			$this->addColumn('entry_time', array(
				'header'    => Mage::helper('orderlog')->__('In Time'),
				'index'     => 'entry_time',
				'type'      => 'datetime',
			));

			$this->addColumn('out_time', array(
				'header'    => Mage::helper('orderlog')->__('Out Time'),
				'index'     => 'out_time',
				'type'      => 'datetime',
			));

			$this->addColumn('totaltime', array(
				'header'    => Mage::helper('orderlog')->__('Total Time'),
				'index'     => 'totaltime',
				'type'      => 'text',
				"filter"    => false,
        		"sortable"  => false,
			));

			$this->addColumn('ip_address', array(
				'header'    => Mage::helper('orderlog')->__('Ip Address'),
				'index'     => 'ip_address',
				'type'      => 'text',
			));

		$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
		$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

			return parent::_prepareColumns();
	}

	public function getRowUrl($row)
	{
		   return '#';
	}			

}