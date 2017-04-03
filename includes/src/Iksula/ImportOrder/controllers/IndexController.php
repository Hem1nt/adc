<?php
class Iksula_ImportOrder_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
		
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Old-order"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getUrl('',array('_secure'=>true))
		   ));

      $breadcrumbs->addCrumb("old-order", array(
                "label" => $this->__("Old-order"),
                "title" => $this->__("Old-order")
		   ));

      $this->renderLayout(); 
	  
    }
	
}