<?php
class Iksula_Shipmentinfo_Adminhtml_ShipmentinfobackendController extends Mage_Adminhtml_Controller_Action
{
	protected function _isAllowed(){
        // return true;
        return Mage::getSingleton('admin/session')->isAllowed('shipmentinfo/shipmentinfobackend');  
    }

	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Shipmentinfo"));
	   $this->renderLayout();
    }
}