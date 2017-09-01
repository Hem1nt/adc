<?php
class Iksula_Overrides_Model_System_Config_Source_Payment_Allpaymentcode
{
    public function toOptionArray()
    {
       $payments = Mage::getSingleton('payment/config')->getActiveMethods();
 
       $methods = array(array('value'=>'all', 'label'=>Mage::helper('adminhtml')->__('Pay Now')));
 
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