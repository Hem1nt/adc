<?php
Class Iksula_Frontend_Model_Status
{
 
	public function toOptionArray()
    {
 	    $status = Mage::getModel('sales/order_status')->getResourceCollection()->getData();

		foreach ($status as $key) {
			$methods[] = array(
                'label'   => $key['label'],
                'value' => $key['status'],
            );
		}	
		
        return $methods;
     } 
 

}