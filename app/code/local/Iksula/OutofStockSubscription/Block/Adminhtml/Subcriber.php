<?php

class Iksula_OutofStockSubscription_Block_Adminhtml_Subcriber extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()

    {
        parent::__construct();
        $this->_blockGroup = 'outofstocksubscription';
        $this->_controller = 'adminhtml_subcriber';
        $this->_headerText = Mage::helper('outofstocksubscription')->__('Out of Stock Subscribers');
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
