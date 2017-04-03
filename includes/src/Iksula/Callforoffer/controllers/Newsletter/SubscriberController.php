<?php
require_once "Mage/Newsletter/controllers/SubscriberController.php";  
class Iksula_Callforoffer_Newsletter_SubscriberController extends Mage_Newsletter_SubscriberController{
	public function newSubAction() {
		
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            //echo $this->getRequest()->getPost('email'); exit;
            $session            = Mage::getSingleton('core/session');
            $customerSession    = Mage::getSingleton('customer/session');
            $email              = (string) $this->getRequest()->getPost('email');
            $model = Mage::getModel('newsletter/subscriber');
            $collection = $model->getCollection();
            $collection->addFieldToFilter('subscriber_email', $email);
            if($collection->getData('subscriber_email')) {
                echo $this->__('You have already subscribe to us.');
                exit;
            }
            try {
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    echo $this->__('Please enter a valid email address.');
                    exit;
                }

                if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 && 
                    !$customerSession->isLoggedIn()) {
                    echo $this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::helper('customer')->getRegisterUrl());
                    exit;
                }


                $ownerId = Mage::getModel('customer/customer')
                        ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                        ->loadByEmail($email)
                        ->getId();
                if ($ownerId !== null && $ownerId != $customerSession->getId()) {
                    echo $this->__('This email address is already assigned to another user.');
                    exit;
                }

                $status = Mage::getModel('newsletter/subscriber')->subscribe($email);
                if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                    echo $this->__('Confirmation request has been sent.'); exit;
                }
                else {
                    echo $this->__('Thank you for your subscription.');
                    $cheetahIds = explode("#", Mage::getModel('core/variable')->loadByCode('cheetah_newsletter')->getValue('plain'));
                    $eid = $cheetahIds[0];
                    $aid = $cheetahIds[1];
                    $callOfferModel = Mage::getModel('callforoffer/callforoffers');
                    $customerName = $callOfferModel->getUserName($email);
                    $callOfferModel->CheetaApi($email, $eid, $aid, $customerName['firstname'], $customerName['lastname']);
                    exit;
                }
            }
            catch (Mage_Core_Exception $e) {
                $session->addException($e, $this->__('There was a problem with the subscription: %s', $e->getMessage()));
            }
            catch (Exception $e) {
                $session->addException($e, $this->__('There was a problem with the subscription.'));
            }
        }
        //$this->_redirectReferer();
    }
}
				