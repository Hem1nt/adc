<?php
class Iksula_Shipmentinfo_Adminhtml_ShipmentinfobackendController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Shipmentinfo"));
	   $this->renderLayout();
    }
}