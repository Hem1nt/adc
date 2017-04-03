<?php
class Iksula_Configfile_Model_System_Config_Source_Attribute_List{

    public function toOptionArray(){
        
        $storedArray = Mage::getResourceModel('catalog/product_attribute_collection');
        $storedArray->addFieldToFilter('is_visible_on_front',1);     

        $options = array(); 
        foreach($storedArray as $attribute){
          $options[] = array(
            'value' => $attribute->getAttributeCode(),
            'label' => $attribute->getFrontendLabel()
            );
      }
      
      return $options;
  }
}
