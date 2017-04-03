<?php
class Iksula_Blacklist_Block_Adminhtml_System_Config_Blacklist_Address extends Iksula_Blacklist_Block_Adminhtml_System_Config_Abstract{

    protected $_getCountryTypeRenderer;

    protected function _prepareToRender(){
        $this->addColumn(
            'address1',
            array(
                'label'     => Mage::helper('blacklist')->__('Street Address Line1'),
                'renderer'  => '',
                'style'     => 'width:100px',
            )
        );
        $this->addColumn(
            'address2',
            array(
                'label'     => Mage::helper('blacklist')->__('Street Address Line2'),
                'renderer'  => '',
                'style'     => 'width:100px',
            )
        );
        $this->addColumn(
            'city',
            array(
                'label'     => Mage::helper('blacklist')->__('City'),
                'renderer'  => '',
                'style'     => 'width:80px',
            )
        );
        $this->addColumn(
            'country',
            array(
                'label'     => Mage::helper('blacklist')->__('Country'),
                'renderer'  => $this->_getCountryRenderer(),
            )
        );
        $this->addColumn(
            'zipcode',
            array(
                'label'     => Mage::helper('blacklist')->__('Zip/Postal Code'),
                'renderer'  => '',
                'style'     => 'width:100px',
            )
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('blacklist')->__('Add Address');
    }

    protected function _getCountryRenderer(){
        if (!$this->_getCountryTypeRenderer) {
            $this->_getCountryTypeRenderer = $this->getLayout()->createBlock(
                'blacklist/adminhtml_system_config_blacklist_country',
                '',
                array('is_render_to_js_template' => true)
            );
            
            $this->_getCountryTypeRenderer->setClass('blacklist_country_select');
            $this->_getCountryTypeRenderer->setExtraParams('style="width:80px"');

        }
        return $this->_getCountryTypeRenderer;
    }

    protected function _prepareArrayRow(Varien_Object $row){
        $row->setData(
            'option_extra_attr_' . $this->calcOptionHash($row->getData('country'), $this->_getCountryRenderer()),
            'selected="selected"'
        );
    }

}