<?php
class Iksula_Configfile_Model_System_Config_Source_Customerattribute_List{

    public function toOptionArray(){        
    	$storedArray = Mage::getSingleton('sales/order_config')->getStatuses();
    	$options = array(); 
    	
        $type='customer';  
        $model = 'customer/attribute_collection';  

        $this->type=$type;  
        $collection = Mage::getResourceModel($model)  
        ->setEntityTypeFilter( Mage::getModel('eav/entity')->setType($type)->getTypeId() )  
        ->addFieldToFilter('is_system','0')  
        ->addVisibleFilter();
        // echo "<pre>";
        foreach($collection as $index =>$attribute){
            // print_r($attribute->getData());
            $options[] = array(
                'value' => $attribute->getAttributeCode(),
                'label' => $attribute->getFrontendLabel()
            );
        }   
        // exit();
       return $options;
    }
}
