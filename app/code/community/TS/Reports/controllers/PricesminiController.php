<?php
require_once("TS/Reports/controllers/AbstractController.php");

class TS_Reports_PricesminiController extends TS_Reports_AbstractController {
	
	/*****  Ajaxi jaoks *****/
	public function gridAction(){
		$this->loadLayout();
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('ts_reports/list_pricesmini_grid')->toHtml()
		);
	}
	
	public function indexAction(){	
		$this->loadLayout();
		$this->_initMenu('ts_reports/list_pricesmini', array('TS Reports','List of Prices (Mini)') );
		$this->_addContent($this->getLayout()->createBlock('ts_reports/list_pricesmini'));
		$this->renderLayout();
    }
 
    public function importAction(){
		$this->_redirect('adminhtml/system_config/edit', array('section' => 'ts_reports'));
    }
 

}
