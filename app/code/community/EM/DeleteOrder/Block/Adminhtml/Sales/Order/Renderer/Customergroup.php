<?php

class EM_DeleteOrder_Block_Adminhtml_Sales_Order_Renderer_Customergroup extends  Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
          $orderId = $row->getId();
          $orderData = Mage::getModel('sales/order')->load($orderId);
          $custEmail = $orderData->getCustomerEmail();
          $custId = $orderData->getCustomerId();
        	$customerGroupId = $orderData->getCustomerGroupId();
          $custData = Mage::getModel('customer/customer')->load($custId);
          if(empty($custId)){
            $orderCollection = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_email',$custEmail);
          $orderHelper = Mage::helper('frontend/order');
          $customersOrdersCount = $orderHelper->getCustomersOrdersCount($custEmail);
          if($customersOrdersCount >= 3){
               return 'Premium Customer'; 
            }elseif($customersOrdersCount >= 1 && $customersOrdersCount <= 2){
                return 'Regular Customer';
            }elseif($customersOrdersCount == 0){
                return 'Guest Group';
            }
          }else{
               $custGroupId = Mage::getModel('customer/group')->load($custData->getGroupId());
          	   return $custGroupId->getCustomerGroupCode();
          }
    }
}
?>
