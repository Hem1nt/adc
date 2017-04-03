<?php
class Manoj_Abandoned_Model_Option{	

	  public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Hours')),
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Day')),
        );
        
    }	

      public function toOptionArray2()
    {
          $methods = array(array('value'=>'','label'=>Mage::helper('adminhtml')->__('--Please Select--')));

        $activeCarriers = Mage::getSingleton('shipping/config')->getActiveCarriers();
        foreach($activeCarriers as $carrierCode => $carrierModel)
        {
           $options = array();
           if( $carrierMethods = $carrierModel->getAllowedMethods() )
           {
               foreach ($carrierMethods as $methodCode => $method)
               {
                    $code= $carrierCode.'_'.$methodCode;
                    $options[]=array('value'=>$code,'label'=>$method);

               }
               $carrierTitle = Mage::getStoreConfig('carriers/'.$carrierCode.'/title');

           }
            $methods[]=array('value'=>$options,'label'=>$carrierTitle);
        }
        // print_r($methods);
        // exit();
        return $methods;

    }

     public function toOptionArray3()
    {
      $configValue = Mage::getStoreConfig('general/herbalcategory/category_id',Mage::app()->getStore());
      if(!$configValue){
        $configValue = 2;
      }
      // $categories = Mage::getModel('catalog/category')->getCategories($configValue);                     
      // $options = array();                       
      // $options[] = array(                
      //   'label' => Mage::helper('adminhtml')->__('-- Please Select a Category --'),                
      //   'value' => ''            
      //   );                
      // foreach($categories as $category) {       
      //   if($category->getName()!=''){        
      //     $options[] = array(           
      //       'label' => $category->getName(),           
      //       'value' => $category->getId()        
      //       );        
      //   }    
      // }      
      $rootCategoryId = Mage::app()->getStore()->getRootCategoryId();
      $catModel = Mage::getModel('catalog/category');
      $_category = Mage::getModel('catalog/category')->load($configValue);
      $_subcategories = $_category->getChildrenCategories();//->addAttributeToFilter('level',3);

      foreach ($_subcategories as $key => $_category) {
        $arry[$_category->getId()] = $_category->getName();
        $children = explode(',',$_category->getChildren());
        if($children):
          foreach (array_filter($children) as $key => $value) {
            $childName = $catModel->load($value);
            $arry[$childName->getId()]='-> '.$childName->getName();
            $chid_children = explode(',',$childName->getChildren());
            if(array_filter($chid_children)):
              foreach ($chid_children as $key => $value2) {
               $childchildName = $catModel->load($value2);
               $arry[$childchildName->getId()]='--> '.$childchildName->getName();
             }
             endif;
           }
           endif;

         }
      foreach($arry as $index => $category) {       
          $options[] = array(           
            'label' => $category,           
            'value' => $index        
            );        
      }      
      return $options;
    }
}