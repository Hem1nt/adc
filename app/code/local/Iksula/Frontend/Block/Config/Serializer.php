<?php
class Iksula_Frontend_Block_Config_Serializer
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    protected $_paymentstatus;

    public function _prepareToRender()
    {
        $this->addColumn('payment_method', array(
            'label' => Mage::helper('frontend')->__('Payment Method'),
            'renderer' => $this->_getPaymentCode(),
            'style' => 'width:50px',

        ));

        $this->addColumn('subtotal', array(
            'label' => Mage::helper('frontend')->__('Subtotal'),
            'style' => 'width:100px',
        ));

        $this->addColumn('shipping_amount', array(
            'label' => Mage::helper('frontend')->__('Shipping Amount'),
            'style' => 'width:100px',
        ));

        $this->addColumn('new_shipping_amount', array(
            'label' => Mage::helper('frontend')->__('Greater Shipping Amount'),
            'style' => 'width:100px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('frontend')->__('Add');
    }

  

    protected function  _getPaymentCode() 
    {
        if (!$this->_paymentstatus) {
            $this->_paymentstatus = $this->getLayout()->createBlock(
                'frontend/config_adminhtml_form_field_shipment', '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_paymentstatus;
    }



    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getPaymentCode()
                ->calcOptionHash($row->getData('payment_method')),
            'selected="selected"'
        );
    }
}