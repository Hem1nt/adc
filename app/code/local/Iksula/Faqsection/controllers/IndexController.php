<?php
class Iksula_Faqsection_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("FAQs/HELP"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("faqs/help", array(
                "label" => $this->__("FAQs/HELP"),
                "title" => $this->__("FAQs/HELP")
		   ));

      $this->renderLayout(); 
	  
    }
}