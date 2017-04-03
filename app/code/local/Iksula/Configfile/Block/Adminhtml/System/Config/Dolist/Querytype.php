<?php

class Iksula_Configfile_Block_Adminhtml_System_Config_Dolist_Querytype extends Iksula_Configfile_Block_Adminhtml_System_Config_Abstract
{
    protected function _prepareToRender(){
        $this->addColumn(
            'query',
            array(
                'label'     => Mage::helper('configfile')->__('Query'),
                'renderer'  => '',
            )
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('configfile')->__('Add Query');
    }

}