<?php
class MW_Affiliate_Block_Adminhtml_Affiliatewithdrawnpending extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct() {
		$this->_controller = 'adminhtml_affiliatewithdrawnpending';
		$this->_blockGroup = 'affiliate';
		$this->_headerText = Mage::helper('affiliate')->__('Withdrawal History Pending');
			
		parent::__construct();
		$this->_removeButton('add');
		
	}
}