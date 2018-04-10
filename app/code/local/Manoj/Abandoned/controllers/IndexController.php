<?php
class Manoj_Abandoned_IndexController extends Mage_Core_Controller_Front_Action{
  public function IndexAction() {

   $this->loadLayout();   
   $this->getLayout()->getBlock("head")->setTitle($this->__("Abandoned"));
   $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
   $breadcrumbs->addCrumb("home", array(
    "label" => $this->__("Home Page"),
    "title" => $this->__("Home Page"),
    "link"  => Mage::getBaseUrl()
    ));

   $breadcrumbs->addCrumb("abandoned", array(
    "label" => $this->__("Abandoned"),
    "title" => $this->__("Abandoned")
    ));

   $this->renderLayout(); 

 }
 public function testAction(){
  // echo "TEST";exit;
   //$abandonedcart = Mage::helper('abandoned');
  // $abandonedcart->Synchronize();
  // $abandonedcart = Mage::helper('abandoned');
    // $cust_email_id = 'manoj.chowrasiya@iksula.com'; 
    // $abandonedcart->Synchronize();  
  // Mage::log(date(),null,'abondent_call_function.log');   
  // exit;
   //$abandonedcart->synchCart();   
   //$abandonedcart->mailsend2();
    // $abandonedcart->testmailsend();
}

public function sendemailAction(){
  // echo 'send email';exit();
  $abandonedcart = Mage::helper('abandoned');
  $abandonedcart->synchCart();   
  Mage::log(date(),null,'abondent_call_function.log');  
  $abandonedcart->mailsend2();
}
 public function duplicateAction(){
  // echo "TEST";exit;
  // $abandonedcart = Mage::helper('abandoned');
  // $abandonedcart->Synchronize();
  $abandonedcart = Mage::helper('abandoned');
    // $cust_email_id = 'manoj.chowrasiya@iksula.com'; 
    // $abandonedcart->Synchronize();   
  // $abandonedcart->synchCart();   
  // $abandonedcart->mailsend();
    $abandonedcart->testmailsend();
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
public function loginAction()
{
  // $email = 'pavlo2469@hotmail.com'; 
  // abandoned/index/login
  $email = Mage::getStoreConfig('general/setting/customerlogincheck');
  $customer = Mage::getModel('customer/customer');
  $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
  $customer->loadByEmail($email);
  Mage::getSingleton('customer/session')->loginById($customer->getId());
  $this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
}
}