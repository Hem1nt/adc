<?php
class TS_Reports_Block_List_Container extends Mage_Adminhtml_Block_Widget_Grid_Container { 
     
    public function __construct(){
        $this->_blockGroup = 'ts_reports';           
        parent::__construct();
		$this->removeButton('add');
		$this->_addButton('import', array( 
			'label' => Mage::helper('adminhtml')->__('Import'), 
			'onclick' => 'setLocation(\'' . $this->getUrl('*/*/import') . '\')', 
			'class' => 'save',	
		), 0);
		$this->_addButton('rule_check', array( 
			'label' => Mage::helper('adminhtml')->__('Re-check for rules'), 
			'onclick' => 'setLocation(\'' . $this->getUrl('*/*/rulecheck') . '\')', 
			'class' => 'save',	
		), -1);
    }  
	
}  
