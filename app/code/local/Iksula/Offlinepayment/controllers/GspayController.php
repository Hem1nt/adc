<?php
class Iksula_Offlinepayment_GspayController extends Mage_Core_Controller_Front_Action{
    
    public function IndexAction() {
      
  	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Online Payment"));
      $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $this->renderLayout(); 
	  
    }

    public function processPaymentAction() {
       $this->loadLayout();   
       $this->getLayout()->getBlock("head")->setTitle($this->__("Online Payment"));
       $this->renderLayout(); 
    }

    public function pendingAction() {
      
        $this->loadLayout();   
        $this->getLayout()->getBlock("head")->setTitle($this->__("Online Payment"));
        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $this->renderLayout(); 
    
    }

    public function successAction() {
      
        $request = Mage::app()->getRequest()->getParams();

        Mage::log($request,null,'success_responce.log');

        $transactionTransactionID = $request['transactionTransactionID'];
        $transactionStatus = $request['transactionStatus'];
        $customerOrderID = $request['customerOrderID'];
        $transactionAmount = $request['transactionAmount'];
        $_order = Mage::getModel('sales/order')->loadByIncrementId($customerOrderID);
    
        if($_order->getId()){             
            $this->loadLayout(); 
            $this->renderLayout();
        }else{
            Mage::getSingleton('core/session')->addError('Request Order is not exists in the System');
            Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl());
        }   
    
    }


    public function failureAction() {

      $request = Mage::app()->getRequest()->getParams();

      Mage::log($request,null,'failure_responce.log');

      $transactionTransactionID = $request['transactionTransactionID'];
      $transactionStatus = $request['transactionStatus'];
      $customerOrderID = $request['customerOrderID'];
      $transactionAmount = $request['transactionAmount'];
      $_order = Mage::getModel('sales/order')->loadByIncrementId($customerOrderID);

      if($_order->getId()){             
        $this->loadLayout(); 
        $this->renderLayout();
      }else{
        Mage::getSingleton('core/session')->addError('Request Order is not exists in the System');
        Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl());
      }

    }
}