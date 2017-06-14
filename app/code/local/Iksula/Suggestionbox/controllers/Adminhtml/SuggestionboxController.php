<?php

class Iksula_Suggestionbox_Adminhtml_SuggestionboxController extends Mage_Adminhtml_Controller_Action
{
	protected function _isAllowed(){
        // return true;
        return Mage::getSingleton('admin/session')->isAllowed('suggestionbox');  
    }

	protected function _initAction(){
		$this->loadLayout()->_setActiveMenu("suggestionbox/suggestionbox")->_addBreadcrumb(Mage::helper("adminhtml")->__("Suggestion Box  Manager"),Mage::helper("adminhtml")->__("Suggestion Box Manager"));
		return $this;
	}

	public function indexAction(){
	    $this->_title($this->__("Suggestion Box"));
	    $this->_title($this->__("Manager Suggestion Box"));
		$this->_initAction();
		$this->renderLayout();
	}

	public function exportCsvAction(){
		$fileName   = 'suggestionbox.csv';
		$grid       = $this->getLayout()->createBlock('suggestionbox/adminhtml_suggestionbox_grid');
		$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
	} 

	public function exportExcelAction()	{
		$fileName   = 'suggestionbox.xml';
		$grid       = $this->getLayout()->createBlock('suggestionbox/adminhtml_suggestionbox_grid');
		$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
	}
}
