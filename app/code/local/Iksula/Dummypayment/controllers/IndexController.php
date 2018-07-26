<?php
class Iksula_Dummypayment_IndexController extends Mage_Core_Controller_Front_Action{
  public function IndexAction() 
  {
	  $this->loadLayout();
    $this->renderLayout(); 	  
  }
}