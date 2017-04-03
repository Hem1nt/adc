<?php

class EM_DeleteOrder_Block_Adminhtml_Sales_Order_Renderer_Lastcomment extends  Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {         
    	$message = '';                   
        $salesCollection = Mage::getModel("sales/order_status_history")->getCollection()->addFieldToFilter("parent_id",$row->getEntityId());
        if($salesCollection->getLastItem()->getComment() != ""){
            // print_r($salesCollection->getLastItem()->getComment());
            $message =  "<span title='".$salesCollection->getLastItem()->getComment()."' style='color:rgb(3, 32, 150)'>Last Comment</span>";
        }

        echo $message;       

    }
}
?>