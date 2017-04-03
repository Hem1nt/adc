<?php
class Iksula_Blacklist_Block_Adminhtml_System_Config_Blacklist_Phonenumber extends Iksula_Blacklist_Block_Adminhtml_System_Config_Abstract{

    protected function _prepareToRender(){
        $this->addColumn(
            'phonenumber',
            array(
                'label'     => Mage::helper('blacklist')->__('Blacklist Phonenumber'),
                'renderer'  => '',
            )
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('blacklist')->__('Add Phonenumber');
    }

}