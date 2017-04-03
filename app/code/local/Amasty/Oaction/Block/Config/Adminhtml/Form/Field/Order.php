<?php
class Iksula_PaymentDisplay_Block_Config_Adminhtml_Form_Field_Order
    extends Mage_Core_Block_Html_Select
{
    public function _toHtml()
    {
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();
 
       // $methods = array(array('value'=>'', 'label'=>Mage::helper('adminhtml')->__('--Please Select--')));
        $this->addOption('', 'Select Payment Method');
       foreach ($payments as $paymentCode=>$paymentModel) {
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            $this->addOption($paymentCode, $paymentTitle);
        }
 
        // return $methods;
        return parent::_toHtml();
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }
}