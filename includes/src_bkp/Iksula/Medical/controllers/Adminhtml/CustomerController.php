<?php
require_once "Mage/Adminhtml/controllers/CustomerController.php";  
class Iksula_Medical_Adminhtml_CustomerController extends Mage_Adminhtml_CustomerController{

	 public function medicalAction(){
	 //	echo "manoj";exit;
	    $this->_initCustomer();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/customer_edit_tab_medical','admin.customer.medical')->setCustomerId(Mage::registry('current_customer')->getId())
            ->setUseAjax(true)
            ->toHtml()
            );
    }

}
				