<?php

class Iksula_Clicktoform_Adminhtml_ClicktoformController extends Mage_Adminhtml_Controller_Action
{		

	protected function _isAllowed(){
	        return Mage::getSingleton('admin/session')->isAllowed('system/config'); // It return true/false with acl set with user
	    }


	    protected function _initAction()
	    {
	    	$this->loadLayout()->_setActiveMenu("clicktoform/clicktoform")->_addBreadcrumb(Mage::helper("adminhtml")->__("Customer Manager"),Mage::helper("adminhtml")->__("Customer Manager"));
	    	return $this;
	    }
	    public function indexAction() 
	    {
	    	$this->_title($this->__("Customer"));
	    	$this->_title($this->__("Manager Customer"));

	    	$this->_initAction();
	    	$this->renderLayout();

	    }
	    public function editAction()
	    {			    
	    	$this->_title($this->__("Customer Calling Info"));
	    	$this->_title($this->__("CustomerInfo"));
	    	$this->_title($this->__("Edit Item"));

	    	$id = $this->getRequest()->getParam("id");
	    	$model = Mage::getModel("clicktoform/clicktoform")->load($id);
	    	if ($model->getId()) {
	    		Mage::register("clicktoform_data", $model);
	    		$this->loadLayout();
	    		$this->_setActiveMenu("grid/customerinfo");
	    		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Customer calling Manager"), Mage::helper("adminhtml")->__("Customer calling Manager"));

	    		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
	    		$this->_addContent($this->getLayout()->createBlock("clicktoform/adminhtml_clicktoform_edit"))->_addLeft($this->getLayout()->createBlock("clicktoform/adminhtml_clicktoform_edit_tabs"));
	    		$this->renderLayout();
	    	} 
	    	else {
	    		Mage::getSingleton("adminhtml/session")->addError(Mage::helper("clicktoform")->__("Item does not exist."));
	    		$this->_redirect("*/*/");
	    	}
	    }
	    public function newAction()
	    {

	    	$this->_title($this->__("Customer Calling Info"));
	    	$this->_title($this->__("CustomerInfo"));
	    	$this->_title($this->__("New Item"));

	    	$id   = $this->getRequest()->getParam("id");
	    	$model = Mage::getModel("clicktoform/clicktoform")->load($id);

	    	$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
	    	if (!empty($data)) {
	    		$model->setData($data);
	    	}

	    	Mage::register("clicktoform_data", $model);

	    	$this->loadLayout();
	    	$this->_setActiveMenu("admingrid/customerinfo");

	    	$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

	    	$this->_addContent($this->getLayout()->createBlock("clicktoform/adminhtml_clicktoform_edit"))->_addLeft($this->getLayout()->createBlock("clicktoform/adminhtml_clicktoform_edit_tabs"));

	    	$this->renderLayout();

	    }
	    public function saveAction()
	    {

	    	$post_data=$this->getRequest()->getPost();
	    	if ($post_data) {
			try {
	    			$model = Mage::getModel("clicktoform/clicktoform")
	    			->addData($post_data)
	    			->setId($this->getRequest()->getParam("id"))
	    			->save();
	    			//print_r($model->getData());die;

	    			Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Customer info was successfully saved"));
	    			Mage::getSingleton("adminhtml/session")->setClicktoformData(false);

	    			if ($this->getRequest()->getParam("back")) {
	    				$this->_redirect("*/*/edit", array("id" => $model->getId()));
	    				return;
	    			}
	    			$this->_redirect("*/*/");
	    			return;
	    		} 
	    		catch (Exception $e) {
	    			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
	    			Mage::getSingleton("adminhtml/session")->setClicktoformData($this->getRequest()->getPost());
	    			$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
	    			return;
	    		}

	    	}
	    	$this->_redirect("*/*/");
	    }



	    public function deleteAction()
	    {
	    	if( $this->getRequest()->getParam("id") > 0 ) {
	    		try {
	    			$model = Mage::getModel("clicktoform/clicktoform");
	    			$model->setId($this->getRequest()->getParam("id"))->delete();
	    			Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
	    			$this->_redirect("*/*/");
	    		} 
	    		catch (Exception $e) {
	    			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
	    			$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
	    		}
	    	}
	    	$this->_redirect("*/*/");
	    }


	    public function massRemoveAction()
	    {
	    	try {
	    		$ids = $this->getRequest()->getPost('customeform_ids', array());
	    		foreach ($ids as $id) {
	    			$model = Mage::getModel("clicktoform/clicktoform");
	    			$model->setId($id)->delete();
	    		}
	    		Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Customer's info was successfully removed"));
	    	}
	    	catch (Exception $e) {
	    		Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
	    	}
	    	$this->_redirect('*/*/');
	    }


	/**
	 * Export order grid to CSV format
	 */
	public function exportCsvAction()
	{
		$fileName   = 'customercallinfo.csv';
		$grid       = $this->getLayout()->createBlock('clicktoform/adminhtml_clicktoform_grid');
		$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
	} 
	/**
	 *  Export order grid to Excel XML format
	 */
	public function exportExcelAction()
	{
		$fileName   = 'customercallinfo.xml';
		$grid       = $this->getLayout()->createBlock('clicktoform/adminhtml_clicktoform_grid');
		$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
	}
}