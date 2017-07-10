<?php
class Iksula_Echecksteps_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {

    $this->loadLayout();   
    $this->getLayout()->getBlock("head")->setTitle($this->__("Echeck working flow"));
          $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
       ));

      $breadcrumbs->addCrumb("echeck working flow", array(
                "label" => $this->__("Echeck working flow"),
                "title" => $this->__("Echeck working flow")
       ));

      $this->renderLayout(); 
      
    }
}