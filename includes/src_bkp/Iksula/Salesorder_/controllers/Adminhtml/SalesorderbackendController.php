<?php
class Iksula_Salesorder_Adminhtml_SalesorderbackendController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {
		$this->loadLayout();
		$this->_title($this->__("Sales Order Report"));
		$this->renderLayout();
    }
	
	public function testAction(){
	
		
		}
}