<?php
class TS_Reports_Model_Resource_Order_Aggregated_Collection extends Mage_Sales_Model_Resource_Report_Order_Collection {
   
    protected $_aggregationTable = 'ts_reports/order_aggregated';

    public function __construct(){
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init($this->_aggregationTable, 'period_status');
        $this->setConnection($this->getResource()->getReadConnection());
    }
	
	protected function _initSelect(){
		parent::_initSelect();
	}
	
}