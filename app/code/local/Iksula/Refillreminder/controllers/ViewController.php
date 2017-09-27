<?php
class Iksula_Refillreminder_ViewController extends Mage_Core_Controller_Front_Action{

    public function indexAction()
    {

        $customerSession = Mage::getSingleton('customer/session');
        if(!$customerSession->isLoggedIn()) {
            $this->getResponse()->setRedirect(Mage::getUrl('customer/account'));
        }
        

		$this->loadLayout();   
		$this->getLayout()->getBlock("head")->setTitle($this->__("Refill Reminder"));
		$this->renderLayout();
    }
    public function AjaxReminderAction() {
    	$block = $this->getLayout()->createBlock("core/template")->setTemplate("refillreminder/update.phtml");
		echo $block->toHtml();
    }
    public function AjaxOrderReminderAction() {
        $block = $this->getLayout()->createBlock("core/template")->setTemplate("refillreminder/orders.phtml");
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
    public function DeleteOrderAction() {
        $remind_id = $this->getRequest()->getParam('remind_id');
        $customerSession = Mage::getSingleton('customer/session');
        $refillModel = Mage::getModel('refillreminder/orderreminder');
        if($customerSession->isLoggedIn()) {
            $email = $customerSession->getCustomer()->getEmail();
        }
        //echo $email;
        $collection = $refillModel->getCollection();
        $collection->addFieldToFilter('order_inc_id', $remind_id);
        $collection->addFieldToFilter('customer_email', $email);
        //echo "<pre>"; print_r(get_class_methods($collection)); exit;
        if($collection->getSize()) {
            $refillModel->setId($remind_id);
            $refillModel->delete();
        }
        $this->AjaxOrderReminderAction();
    }
}