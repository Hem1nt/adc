<?php

class Iksula_Configfile_Block_Adminhtml_System_Config_Dolist_Queryemail extends Iksula_Configfile_Block_Adminhtml_System_Config_Abstract
{
    protected $_queryTypeRenderer;

    protected function _prepareToRender(){
        $this->addColumn(
            'query_type_data',
            array(
                'label'     => Mage::helper('configfile')->__('Query Type'),
                'renderer'  => $this->_getQueryTypeRenderer(),
            )
        );
        $this->addColumn(
            'email_id',
            array(
                'label'     => Mage::helper('configfile')->__('Email Id'),
                'renderer'  => '',
                'style'     => 'width:100px',
            )
        );
        $this->addColumn(
            'copy_email_id',
            array(
                'label'     => Mage::helper('configfile')->__('Copy Email Id'),
                'renderer'  => '',
                'style'     => 'width:100px',
            )
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('configfile')->__('Add Email');
    }


    protected function _getQueryTypeRenderer(){
        if (!$this->_queryTypeRenderer) {
            $this->_queryTypeRenderer = $this->getLayout()->createBlock(
                'configfile/adminhtml_system_config_dolist_querylist',
                '',
                array('is_render_to_js_template' => true)
            );
            
            $this->_queryTypeRenderer->setClass('dolist_email_template_select');
            $this->_queryTypeRenderer->setExtraParams('style="width:100px"');
        }
        return $this->_queryTypeRenderer;
    }

    protected function _prepareArrayRow(Varien_Object $row){
        $row->setData(
            'option_extra_attr_' . $this->calcOptionHash($row->getData('query_type_data'), $this->_getQueryTypeRenderer()),
            'selected="selected"'
        );
    }
}