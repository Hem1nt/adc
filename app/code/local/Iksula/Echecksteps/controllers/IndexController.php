<?php
class Iksula_Echecksteps_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {

    $this->loadLayout();   
    $this->getLayout()->getBlock("head")->setTitle($this->__("How eCheck Works"));
          $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
       ));

      $breadcrumbs->addCrumb("how eCheck works", array(
                "label" => $this->__("How eCheck Works"),
                "title" => $this->__("How eCheck Works")
       ));

      $this->renderLayout(); 
      
    }
}