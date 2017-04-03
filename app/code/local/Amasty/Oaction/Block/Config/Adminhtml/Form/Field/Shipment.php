<?php
class Amasty_Oaction_Block_Config_Adminhtml_Form_Field_Shipment
    extends Mage_Core_Block_Html_Select
{
    public function _toHtml()
    {
        $this->addOption('', 'Select Shiiping Carrier');
        $carriersData = array();
        $carriers = Mage::getsingleton("shipping/config")->getAllCarriers();
        foreach($carriers as $code => $method){
            if($code!='googlecheckout'){
                $this->addOption($code, Mage::getStoreConfig("carriers/$code/title"));
            }
        }
        return parent::_toHtml();
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }
}