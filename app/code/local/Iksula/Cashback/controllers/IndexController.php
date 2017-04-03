<?php
class Iksula_Cashback_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Cashback"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("cashback", array(
                "label" => $this->__("Cashback"),
                "title" => $this->__("Cashback")
		   ));

      $this->renderLayout(); 
	  
    }
    public function ViewcashbackAction() {
      
    $this->loadLayout();   
   

      $this->renderLayout(); 
    
    }
}