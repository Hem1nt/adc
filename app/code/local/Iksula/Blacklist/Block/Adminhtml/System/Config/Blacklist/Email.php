<?php
class Iksula_Blacklist_Block_Adminhtml_System_Config_Blacklist_Email extends Iksula_Blacklist_Block_Adminhtml_System_Config_Abstract{

    protected function _prepareToRender(){
        $this->addColumn(
            'email',
            array(
                'label'     => Mage::helper('blacklist')->__('Blacklist Email'),
                'renderer'  => '',
            )
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('blacklist')->__('Add Email');
    }

}