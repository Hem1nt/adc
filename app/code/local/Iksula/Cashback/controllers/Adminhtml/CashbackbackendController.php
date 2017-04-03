<?php
class Iksula_Cashback_Adminhtml_CashbackbackendController extends Mage_Adminhtml_Controller_Action
{

	protected function _isAllowed()
	{
		//return Mage::getSingleton('admin/session')->isAllowed('cashback/cashbackbackend');
		return true;
	}

	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Cashback"));
	   $this->renderLayout();
    }
}