<?php

class EM_DeleteOrder_Block_Adminhtml_Sales_Order_Renderer_Suspicious extends  Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {         
    	$message = '';
        $_orders = Mage::getModel('sales/order')->load($row->getEntityId());                
        /*$salesCollection = Mage::getModel("sales/order_status_history")->getCollection()->addFieldToFilter("parent_id",$row->getEntityId());*/
        $suspiciousOrder = json_decode($_orders->getData('suspicious'),true);
        if($suspiciousOrder['suspicious_value'] != ""){
            $message =  "<span title='".$suspiciousOrder['suspicious_id']."' style='color:red;font-size:20px;'>".$suspiciousOrder['suspicious_value']."</span>";
        }

        echo $message;       

    }
}
?>