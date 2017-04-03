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
}