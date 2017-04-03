<?php

class Iksula_Reports_Block_Adminhtml_Reportview extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct(){
        parent::__construct();
        $this->_blockGroup = 'iksula_reports';
        $this->_controller = 'adminhtml_reportview';
        $this->_headerText = Mage::helper('iksula_reports')->__('Report Details');
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
