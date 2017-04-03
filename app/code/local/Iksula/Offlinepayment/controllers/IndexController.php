<?php
class Iksula_Offlinepayment_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Offlinepayment"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("offlinepayment", array(
                "label" => $this->__("Offlinepayment"),
                "title" => $this->__("Offlinepayment")
		   ));

      $this->renderLayout(); 
	  
    }
}