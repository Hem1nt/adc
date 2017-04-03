<?php
class Iksula_Refillreminder_OrderController extends Mage_Core_Controller_Front_Action{
    public function indexAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock("head")->setTitle($this->__("Refill Reminder"));
        $this->renderLayout();
    }
    public function AjaxReminderAction() {
    	$block = $this->getLayout()->createBlock("core/template")->setTemplate("refillreminder/view.phtml");
		echo $block->toHtml();
    }
    public function DeleteAction() {
    	$remind_id = $this->getRequest()->getParam('remind_id');
		$customerSession = Mage::getSingleton('customer/session');
		$refillModel = Mage::getModel('refillreminder/refillreminder');
		if($customerSession->isLoggedIn()) {
			$email = $customerSession->getCustomer()->getEmail();
		}
		//echo $email;
    	$collection = $refillModel->getCollection();
    	$collection->addFieldToFilter('reminder_id', $remind_id);
    	$collection->addFieldToFilter('customer_email', $email);
    	//echo "<pre>"; print_r(get_class_methods($collection)); exit;
    	if($collection->getSize()) {
    		$refillModel->setId($remind_id);
    		$refillModel->delete();
    	}
    	$this->AjaxReminderAction();
    }
}