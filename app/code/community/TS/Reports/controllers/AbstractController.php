<?php

class TS_Reports_AbstractController extends Mage_Adminhtml_Controller_Action {

	protected function _construct(){
		Mage::getSingleton('core/session', array('name' => 'adminhtml'));
		if(!Mage::getSingleton('admin/session')->isLoggedIn()) {
			$this->_forward('adminhtml/index/login');
			return;
		} else parent::_construct();
	}

	public function isEnabled($model){
		if($model && $model->isEnabled()) return true;
		Mage::getSingleton('adminhtml/session')->addError(
			Mage::helper('ts_reports')->__('The requested page is not avaliable. Check if the page you are trying to access is enabled.')
		);
		$this->_redirect('*/*/');
		return false;
	}
	
	protected function _initMenu($menu,$breadcrumbs){
        $this->_setActiveMenu($menu);
		foreach($breadcrumbs as $crumb){
			$title = Mage::helper('ts_reports')->__($crumb);
			$this->_title($title)->_addBreadcrumb($title,$title);
		}
        return $this;
    }
	
    public function rulecheckAction(){
		$result = Mage::getResourceModel('ts_reports/reportitem')->initRulePrices();
		Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Found catalogrule price for %d record(s).', $result));
		$this->_redirect('*/*/index');
    }
	
}