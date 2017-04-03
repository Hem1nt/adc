<?php
class Iksula_Refillreminder_EditController extends Mage_Core_Controller_Front_Action{
    public function indexAction() {
        
		$this->loadLayout();   
		$this->getLayout()->getBlock("head")->setTitle($this->__("Refill Reminder"));
		$this->renderLayout();
    }
    public function AjaxReminderAction() {

    	$block = $this->getLayout()->createBlock("core/template")->setTemplate("refillreminder/view.phtml");
		echo $block->toHtml();
    }
    public function SaveAction() {

        
        $remind_id = $this->getRequest()->getParam('txtRemindId');
        $remind_days = $this->getRequest()->getParam('remind_days');
        $custPhone = $this->getRequest()->getParam('txtPhone');
        $email = "";
        $customerSession = Mage::getSingleton('customer/session');
        $refillModel = Mage::getModel('refillreminder/refillreminder');
        if($customerSession->isLoggedIn()) {
            $email = $customerSession->getCustomer()->getEmail();
        }
        $collection = $refillModel->getCollection();
        $collection->addFieldToFilter('reminder_id', $remind_id);
        $collection->addFieldToFilter('customer_email', $email);
        //echo $collection->getSelect(); exit;
        if($collection->getSize()) {
            $data = array('last_mail_sent'=>NOW(), 'reminder_days' => $remind_days, 'customer_telephone' => $custPhone); //primary key of reminder table which will update
            $UpdateModel = $refillModel->load()->addData($data);
            try {
                $UpdateModel->setId($remind_id)->save();
                echo "Data has been updated successfully.";
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        //$this->AjaxReminderAction();
    }

    public function SaveOrderReminderAction() {

        $remind_id = $this->getRequest()->getParam('txtRemindId');
        $remind_days = $this->getRequest()->getParam('remind_days');
        $custPhone = $this->getRequest()->getParam('txtPhone');
        $email = "";
        $customerSession = Mage::getSingleton('customer/session');
        $refillModel = Mage::getModel('refillreminder/orderreminder');
        if($customerSession->isLoggedIn()) {
            $email = $customerSession->getCustomer()->getEmail();
        }
        $collection = $refillModel->getCollection();
        $collection->addFieldToFilter('order_inc_id', $remind_id);
        $collection->addFieldToFilter('customer_email', $email);
        //echo $collection->getSelect(); exit;
        if($collection->getSize()) {
            $data = array('last_mail_sent'=>NOW(), 'reminder_days' => $remind_days, 'customer_telephone' => $custPhone); //primary key of reminder table which will update
            $UpdateModel = $refillModel->load()->addData($data);
            try {
                $UpdateModel->setId($remind_id)->save();
                echo "Data has been updated successfully.";
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        //$this->AjaxReminderAction();
    }

    public function orderAction() {
        $this->loadLayout();   
        $this->getLayout()->getBlock("head")->setTitle($this->__("Refill Reminder"));
        $this->renderLayout();
    }
}