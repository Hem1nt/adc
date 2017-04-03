<?php

class Iksula_Configfile_Block_Adminhtml_System_Config_Dolist_Templatemapping extends Iksula_Configfile_Block_Adminhtml_System_Config_Abstract
{
    protected $_magentoTemplateRenderer;

    protected $_dolistemtTemplateRenderer;

    protected function _prepareToRender(){
        $this->addColumn(
            'list_template',
            array(
                'label'     => Mage::helper('configfile')->__('Status'),
                'renderer'  => $this->_getDolistemtTemplateRenderer(),
            )
        );
        $this->addColumn(
            'magento_template',
            array(
                'label'     => Mage::helper('configfile')->__('Magento Template'),
                'renderer'  => $this->_getMagentoTemplateRenderer(),
            )
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('configfile')->__('Add Mapping');
    }

    protected function _getMagentoTemplateRenderer(){
        if (!$this->_magentoTemplateRenderer) {
            $this->_magentoTemplateRenderer = $this->getLayout()->createBlock(
                'configfile/adminhtml_system_config_templatelist',
                '',
                array('is_render_to_js_template' => true)
            );
            
            $this->_magentoTemplateRenderer->setClass('magento_email_template_select');
            $this->_magentoTemplateRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_magentoTemplateRenderer;
    }

    protected function _getDolistemtTemplateRenderer(){
        if (!$this->_dolistemtTemplateRenderer) {
            $this->_dolistemtTemplateRenderer = $this->getLayout()->createBlock(
                'configfile/adminhtml_system_config_dolist_templatelist',
                '',
                array('is_render_to_js_template' => true)
            );
            
            $this->_dolistemtTemplateRenderer->setClass('dolist_email_template_select');
            $this->_dolistemtTemplateRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_dolistemtTemplateRenderer;
    }

    protected function _prepareArrayRow(Varien_Object $row){
        $row->setData(
            'option_extra_attr_' . $this->calcOptionHash($row->getData('magento_template'), $this->_getMagentoTemplateRenderer()),
            'selected="selected"'
        )->setData(
            'option_extra_attr_' . $this->calcOptionHash($row->getData('list_template'), $this->_getDolistemtTemplateRenderer()),
            'selected="selected"'
        );
    }
}