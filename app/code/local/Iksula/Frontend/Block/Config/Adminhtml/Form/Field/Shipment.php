<?php
class Iksula_Frontend_Block_Config_Adminhtml_Form_Field_Shipment
    extends Mage_Core_Block_Html_Select
{
    public function _toHtml()
    {
        $this->addOption('', 'Select Payment Method');
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();
        foreach ($payments as $paymentCode=>$paymentModel) {
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            $this->addOption($paymentCode, $paymentTitle);
        }    
        return parent::_toHtml();
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }
}