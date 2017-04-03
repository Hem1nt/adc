<?php
class Iksula_Configfile_Model_System_Config_Source_Status_List{

    public function toOptionArray(){        
    	$storedArray = Mage::getSingleton('sales/order_config')->getStatuses();
    	$options = array(); 
    	$options[] = array(
                'value' => '',
                'label' => ''
            );
    	foreach($storedArray as $orderid =>$storedTemplate){
    		$options[] = array(
                'value' => $orderid,
                'label' => $storedTemplate
            );
    	}
               
        return $options;
    }
}
