<?php

class Iksula_Categorysales_Block_Adminhtml_Categorysalesview extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct(){
        parent::__construct();
        $this->_blockGroup = 'iksula_categorysales';
        $this->_controller = 'adminhtml_categorysalesview';
        $this->_headerText = Mage::helper('iksula_categorysales')->__('Category Sales Report Details');
        $this->_removeButton('add');
    }

    protected function _prepareLayout()
    {
        $this->setChild( 'grid',
            $this->getLayout()->createBlock( $this->_blockGroup.'/' . $this->_controller . '_grid',
            $this->_controller . '.grid')->setSaveParametersInSession(true) );
        return parent::_prepareLayout();
    }
}
