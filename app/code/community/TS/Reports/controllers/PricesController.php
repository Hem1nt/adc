<?php
require_once("TS/Reports/controllers/AbstractController.php");

class TS_Reports_PricesController extends TS_Reports_AbstractController {
	
	/*****  Ajaxi jaoks *****/
	public function gridAction(){
		$this->loadLayout();
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('ts_reports/list_prices_grid')->toHtml()
		);
	}
	
	public function indexAction(){
		$this->loadLayout();
		$this->_initMenu('ts_reports/list_prices', array('TS Reports','List of Prices') );
		$this->_addContent($this->getLayout()->createBlock('ts_reports/list_prices'));
		$this->renderLayout();
    }
 
    public function importAction(){
		$this->_redirect('adminhtml/system_config/edit', array('section' => 'ts_reports'));
    }
 
	
	public function setPriceTypeAction(){
		$priceType = $this->getRequest()->getParam('type');
		$reportitemIds = $this->getRequest()->getParam('reportitem_id');
		
		if(!is_array($reportitemIds)){
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select record(s).'));
		} else {
			if($priceType == null){
				$count = Mage::getModel('ts_reports/init_reportitems')->massOverride($reportitemIds);
				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Total of %d record(s) were reset.', $count));
				//Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			} else {
				$types = Mage::getModel('ts_reports/types')->getTypeNames();
				if(!isset($types[$priceType])) Mage::getSingleton('adminhtml/session')->addError($this->__('Invalid override link!'));
				else {
					$count = Mage::getModel('ts_reports/init_reportitems')->massOverride($reportitemIds, $priceType);
					Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Total of %d record(s) were overridden.', $count));
				}
			}
		}
		$this->_redirect('*/*/');
	}

}
