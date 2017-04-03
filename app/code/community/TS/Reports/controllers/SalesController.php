<?php

class TS_Reports_SalesController extends Mage_Adminhtml_Controller_Report_Abstract { 
    
	public function refreshAction(){
		Mage::getModel('ts_reports/init_statistics')->init();
		
		$to = $this->getRequest()->getParam('from');
		$this->_redirect('*/*/'.$to);
	}
	
	public function productsAction(){
        $this->_title($this->__('Reports'))
			 ->_title($this->__('Sales'))
			 ->_title($this->__('Products'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE, 'sales');

        $this->_initAction()
            ->_setActiveMenu('ts_reports/sales/products')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Sales Report'), Mage::helper('adminhtml')->__('Products Report'));

        //$gridBlock = $this->getLayout()->getBlock('report_sales.grid'); // automatically generated from block's _controller?
		
        $gridBlock = $this->getLayout()->getBlock('report_products.grid'); //must be in XML!
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form'); //see on ok
        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));
        $this->renderLayout();
    }
	
	
	
	
    public function salesAction(){
        $this->_title($this->__('Reports'))
			 ->_title($this->__('Sales'))
			 ->_title($this->__('Sales'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE, 'sales');

        $this->_initAction()
            ->_setActiveMenu('ts_reports/sales/sales')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Sales Report'), Mage::helper('adminhtml')->__('Sales Report'));

        //$gridBlock = $this->getLayout()->getBlock('report_sales.grid'); // automatically generated from block's _controller?
		
        $gridBlock = $this->getLayout()->getBlock('report_sales.grid'); //must be in XML!
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form'); //see on ok
        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));
        $this->renderLayout();
    }
	
	protected function _showLastExecutionTime($flagCode, $refreshCode){
		$refreshLink = $this->getUrl('*/sales/refresh', array('from' => 'sales'));
		
		$updatedAt = Mage::helper('ts_reports')->getRefreshDate();
		if(empty($updatedAt)){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Statistics haven\'t been compiled. To refresh stastics click <a href="%s">here</a>.', $refreshLink));
		} else {
			$updatedAt = Mage::helper('core')->formatDate($updatedAt, 'medium', true);
			Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('adminhtml')->__('Last updated: %s. To refresh stastics click <a href="%s">here</a>.', $updatedAt, $refreshLink));
		}
        
        return $this;
    }
	


    /**
     * Export Sales report to CSV format action
     *
     */
    public function exportSalesCsvAction(){
        $fileName   = 'report_sales.csv';
        $grid    	= $this->getLayout()->createBlock('ts_reports/report_sales_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export Sales report to XML format action
     *
     */
    public function exportSalesExcelAction(){        
        $fileName   = 'report_sales.xml';
        $grid    	= $this->getLayout()->createBlock('ts_reports/report_sales_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
	
    /**
     * Export Products report to CSV format action
     *
     */
    public function exportProductsCsvAction(){
        $fileName   = 'report_products.csv';
        $grid    	= $this->getLayout()->createBlock('ts_reports/report_products_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export Products report to XML format action
     *
     */
    public function exportProductsExcelAction(){        
        $fileName   = 'report_products.xml';
        $grid    	= $this->getLayout()->createBlock('ts_reports/report_products_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

}
