<?php

class TS_Reports_Block_Report_Products extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct(){
        $this->_controller = 'report_products';
        $this->_blockGroup = 'ts_reports';
        $this->_headerText = Mage::helper('ts_reports')->__('TS Report of Products');
        parent::__construct();
		
        $this->_removeButton('add');
        $this->addButton('filter_form_submit', array(
            'label'     => Mage::helper('reports')->__('Show Report'),
            'onclick'   => 'filterFormSubmit()'
        ));
    }

    public function getFilterUrl(){
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/products', array('_current' => true));
    }
}
