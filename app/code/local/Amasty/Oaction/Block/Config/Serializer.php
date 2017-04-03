<?php
class Amasty_Oaction_Block_Config_Serializer
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    protected $_shipmentStatus;

    public function _prepareToRender()
    {
        $this->addColumn('shipping_carrier', array(
            'label' => Mage::helper('amoaction')->__('Shipping Carrier'),
            'renderer' => $this->_getShipment(),
            'style' => 'width:50px',

        ));

        $this->addColumn('url', array(
            'label' => Mage::helper('amoaction')->__('Url'),
            'style' => 'width:300px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('amoaction')->__('Add');
    }

  

    protected function  _getShipment() 
    {
        if (!$this->_shipmentStatus) {
            $this->_shipmentStatus = $this->getLayout()->createBlock(
                'amoaction/config_adminhtml_form_field_shipment', '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_shipmentStatus;
    }



    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getShipment()
                ->calcOptionHash($row->getData('shipping_carrier')),
            'selected="selected"'
        );
    }
}