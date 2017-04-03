<?php
class Iksula_Callforoffer_IndexController extends Mage_Core_Controller_Front_Action{
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
    // public function callforoffersregAction()
    // {
    //   Mage::getSingleton('core/session')->unsCallforfreeval();
    //   $callforfreeval = Mage::app()->getRequest()->getParam('callforfree');
    //   Mage::getSingleton('core/session')->setCallforfreeval($callforfreeval);

    // }
      public function callforoffersregAction()
    {
      Mage::getSingleton('core/session')->unsCallforfreeval();
      $callforfreeval = Mage::app()->getRequest()->getParam('callforfree');
      $timetocall = Mage::app()->getRequest()->getParam('timetocall');
      // print_r($timetocall);
      // exit;
      Mage::getSingleton('core/session')->setCallforfreeval($callforfreeval);
      Mage::getSingleton('core/session')->setTimetocall($timetocall);

    }
}