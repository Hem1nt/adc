<?php
class Iksula_Otherpayments_Model_Activpayment
{ 
 public function toOptionArray()
    {
       $payments = Mage::getSingleton('payment/config')->getActiveMethods();
 
       $methods = array(array('value'=>'', 'label'=>Mage::helper('adminhtml')->__('--Please Select--')));
 
       foreach ($payments as $paymentCode=>$paymentModel) {
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            $methods[$paymentCode] = array(
                'label'   => $paymentTitle.' => '.$paymentCode,
                'value' => $paymentCode,
            );
        }
 
        return $methods;
     } 
 
}

?> 

