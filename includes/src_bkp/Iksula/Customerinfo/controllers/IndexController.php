<?php
class Iksula_Customerinfo_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      $this->loadLayout();   
	    $this->renderLayout(); 
	  
    }

    public function saveCustomerInfoAction(){
      $data = $this->getRequest()->getParams();
      // echo '<pre>';print_r($data);
      $datafilter =  Mage::getModel('customerinfo/customerinfo')->getCollection()->addFieldToFilter('email',array('eq'=> $data['email']))->getData();
      // print_r($datafilter);exit; 

      if(empty($datafilter)){
        //echo 12;
        $data1 = Mage::getModel('customerinfo/customerinfo');
        $data1->setData('name',$data['customer_name']);
        $data1->setData('dob',$data['dob']);
        $data1->setData('email',$data['email']);
        $data1->setData('anniversary',$data['anniversary']); 
        $data1->save();
      }else{
        //echo 34;
        $data1 = Mage::getModel('customerinfo/customerinfo')->load($datafilter[0]['id']);
        $data1->setData('name',$data['customer_name']);
        $data1->setData('dob',$data['dob']);
        $data1->setData('email',$data['email']);
        $data1->setData('anniversary',$data['anniversary']); 
        $data1->save();
      }

      echo '<h1>Thank you for providing information</h1>';
    }
    
}