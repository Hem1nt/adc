<?php
class MW_Affiliate_Block_Adminhtml_Affiliatemember extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_affiliatemember';
    $this->_blockGroup = 'affiliate';
    $this->_headerText = Mage::helper('affiliate')->__('Manage Members');
    //$this->_addButtonLabel = Mage::helper('affiliate')->__('Add Program');
    parent::__construct();
    $this->_removeButton('add');
  }
}