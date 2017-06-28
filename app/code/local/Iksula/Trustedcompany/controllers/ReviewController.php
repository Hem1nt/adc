<?php
class Iksula_Trustedcompany_ReviewController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
    $this->loadLayout();   
    $this->getLayout()->getBlock("head")->setTitle($this->__("Trustedcompany"));
          $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
       ));

      $breadcrumbs->addCrumb("trustedcompany", array(
                "label" => $this->__("Trustedcompany"),
                "title" => $this->__("Trustedcompany")
       ));

      $this->renderLayout(); 
    
    } 

    public function TrustAction() {
      $data = Mage::getModel('sales/order')->load(177723);
      echo Mage::helper('trustedcompany')->sendEmail($data);
      exit;
    }

    public function ReviewAction(){
      $this->loadLayout(); 
      $this->renderLayout(); 
    }

    public function ajaxReviewAction(){
        $limit = $this->getRequest()->getParams();
        $limit = $limit['limit'];
        $obj = new Iksula_Trustedcompany_Block_Review();
        echo $html = $obj->getAjaxReview($limit);
    }
}