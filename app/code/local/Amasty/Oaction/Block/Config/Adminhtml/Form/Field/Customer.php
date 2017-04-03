<?php
class Iksula_PaymentDisplay_Block_Config_Adminhtml_Form_Field_Customer
    extends Mage_Core_Block_Html_Select
{
    public function _toHtml()
    {
        // $options = Mage::getResourceSingleton('core/email_template_collection');
        // foreach ($options as $option) {
        //     $this->addOption($option['template_id'], $option['template_code']);
        // }
        $this->addOption('', 'Select Customer');
        $this->addOption('old', 'Old customers');
        $this->addOption('newCustomer1', 'New Customer Before Oct 2015');
        $this->addOption('newCustomer2', 'New Customer After Oct 2015');
        $this->addOption('guest', 'Guest');
        return parent::_toHtml();
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }
}