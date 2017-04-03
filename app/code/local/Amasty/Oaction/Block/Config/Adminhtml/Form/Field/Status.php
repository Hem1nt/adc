<?php
class Iksula_PaymentDisplay_Block_Config_Adminhtml_Form_Field_Status
    extends Mage_Core_Block_Html_Select
{
    public function _toHtml()
    {
        // $options = Mage::getResourceSingleton('core/email_template_collection');
        // foreach ($options as $option) {
        //     $this->addOption($option['template_id'], $option['template_code']);
        // }
        $this->addOption('', 'Select Status');
        $this->addOption('0', 'Disable');
        $this->addOption('1', 'Enable');
        return parent::_toHtml();
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }
}