<?php 


class Iksula_Overrides_Adminhtml_DeleteshipmentController extends Mage_Adminhtml_Controller_action
{
	public function deleteAction()
	{
		$orderId = $this->getRequest()->getParam('order_id'); 		
		if(Mage::getModel('overrides/deleteshipment')->deleteShipment($orderId)){
			$message = $this->__('Shipment has been deleted succcessfully');
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sales')->__($message));
		}else{
			$message = $this->__('There is some problem in delete shipment');
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sales')->__($message));
		}
		
		$url= $this->getUrl("adminhtml/sales_order/view", array('order_id'=>$orderId));

		$this->_redirectUrl($url);    
	 } 	 
} 
