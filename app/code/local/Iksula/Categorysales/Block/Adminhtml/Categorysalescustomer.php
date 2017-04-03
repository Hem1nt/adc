<?php

class Iksula_Categorysales_Block_Adminhtml_Categorysalescustomer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct(){
        parent::__construct();
        $this->_blockGroup = 'iksula_categorysales';
        $this->_controller = 'adminhtml_categorysalescustomer';
        $this->_headerText = Mage::helper('iksula_categorysales')->__('');
        $this->_removeButton('add');
    }

    protected function _prepareLayout()
    {
        // echo "sdvfs";exit;
        // echo  $this->_blockGroup.'/' . $this->_controller . '_grid';exit;
        $this->setChild( 'grid',
            $this->getLayout()->createBlock( $this->_blockGroup.'/' . $this->_controller . '_grid',
            $this->_controller . '.grid')->setSaveParametersInSession(true) );
        return parent::_prepareLayout();
    }
}
