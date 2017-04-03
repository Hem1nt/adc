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
 public function TestAction(){
  //echo "TEST";exit;
  // $abandonedcart = Mage::helper('abandoned');
  // $abandonedcart->Synchronize();
  $abandonedcart = Mage::helper('abandoned');
    // $cust_email_id = 'manoj.chowrasiya@iksula.com'; 
    $abandonedcart->Synchronize();   
  $abandonedcart->mailsend();
    // $abandonedcart->testmailsend();
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