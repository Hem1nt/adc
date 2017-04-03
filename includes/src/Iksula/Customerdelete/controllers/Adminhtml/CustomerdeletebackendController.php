<?php
class Iksula_Customerdelete_Adminhtml_CustomerdeletebackendController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Ratio Calculator"));
	   $this->renderLayout();
    }
     public function productwiseqtyAction()
    {
    	$this->loadLayout();
    	$this->_title($this->__("Reports"));
    	$this->renderLayout();

    }
	
	public function reportstockAction()
	 {	//echo $this->getRequest()->getParam('fromdatestock');
		$fromdate = DateTime::createFromFormat('m/d/Y', $this->getRequest()->getParam('fromdatestock'));
		$todate = DateTime::createFromFormat('m/d/Y', $this->getRequest()->getParam('todatestock'));
		// echo 'manojn';
		// exit;
		$fromdate = $fromdate->format('Y-m-d H:i:s');
		$todate = $todate->format('Y-m-d H:i:s');
		$filename = 'report.csv';
		$content = Mage::helper('customerdelete')->generateMlnList($fromdate,$todate);
		 // $filename = 'report.csv';
	     // $content = Mage::helper('customerdelete')->generateMlnList();
		$this->_prepareDownloadResponse($filename, $content);
	}
	
	public function reportstockstatementAction(){
		$filename = 'customer_report.csv';
		$content = Mage::helper('customerdelete')->generateCustomerreport();
		$this->_prepareDownloadResponse($filename, $content);
	}

	public function customerreportAction(){
		//print_r($this->getRequest()->getParams());
		//exit;
		$fromdate = DateTime::createFromFormat('m/d/Y', $this->getRequest()->getParam('fromdate'));
		$todate = DateTime::createFromFormat('m/d/Y', $this->getRequest()->getParam('todate'));
		$fromdate = $fromdate->format('Y-m-d H:i:s');
		$todate = $todate->format('Y-m-d H:i:s');
		
		$filename = 'customer_report_withfilters.csv';
		$content = Mage::helper('customerdelete')->generateCustomerreportwithfilters($fromdate,$todate);
	 // echo $content;
		$this->_prepareDownloadResponse($filename, $content);
	}
}