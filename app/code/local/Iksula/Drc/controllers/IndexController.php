<?php
class Iksula_Drc_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Titlename"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("titlename", array(
                "label" => $this->__("Titlename"),
                "title" => $this->__("Titlename")
		   ));

      $this->renderLayout(); 
	  
    }
     public function saveAction() //function to save form
    { 
      $email = $this->getRequest()->getPost('email');
      if (filter_var($email, FILTER_VALIDATE_EMAIL))
      {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $requestedBy = $customer->getEmail();
        $requestedByFirstname = $customer->getFirstname();
        $data = Mage::getModel('drc/emaildrc');
          $data->setData('requested_by',$requestedBy);
          $data->setData('email',$email);
          $data->save();
          /*Cart Link Generation S*/
          if($requestedBy)
          {
            $CustomerEmail = $requestedBy;
            $cartUrl = Mage::getBaseUrl().'drc/index/cartreturn?key='.base64_encode($CustomerEmail).'&utm_source=email-cart&utm_medium=email-cart&utm_campaign=email'.'<br>';
          }
          /*Cart Link Generation E*/
    /*Email Function S*/
          $templateId = Mage::getStoreConfig('payment/drc/email_template'); 
          // Set sender information            
          $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
          $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');        
          $sender = array('name' => $senderName,
                      'email' => $senderEmail);    
          // Set recepient information
          $recepientEmail = $email;
          $recepientName = '';  
          // Get Store ID        
          $storeId = Mage::app()->getStore()->getId();
          // Set variables that can be used in email template
          $vars = array('cart_url' => $cartUrl, 'customer_name' => $requestedByFirstname);
          $translate  = Mage::getSingleton('core/translate');
          // Send Transactional Email
          Mage::getModel('core/email_template')
            ->addBcc($senderEmail)
            ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
                
          $translate->setTranslateInline(true);    
    /*Email Function E*/
          Mage::getSingleton('core/session')->addSuccess('Voucher will be emailed you shortly'); 
      }
    }
    public function cartreturnAction()
    {
      $email = Mage::app()->getRequest()->getParam('key');
      $customer = Mage::getModel('customer/customer');
      $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
      $customer->loadByEmail(trim(base64_decode ($email)));
      Mage::getSingleton('customer/session')->loginById($customer->getId());
      $this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
    }
}