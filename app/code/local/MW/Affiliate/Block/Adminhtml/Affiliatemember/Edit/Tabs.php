<?php

class MW_Affiliate_Block_Adminhtml_Affiliatemember_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('affiliatemember_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('affiliate')->__('Affiliate Member Information'));
  }

  protected function _beforeToHtml()
  {   
  	  //zend_debug::dump($this->getRequest()->getParams());die();
  	  $order_id = $this->getRequest()->getParam('orderid');
  	  if(isset($order_id)){
  	  	$content = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_viewtransaction')->toHtml();
  	  } 
  	  else{
  	  	$content = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_transaction')->toHtml();
  	  }
      $this->addTab('form_member_detail', array(
          'label'     => Mage::helper('affiliate')->__('General information'),
          'title'     => Mage::helper('affiliate')->__('General information'),
          'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_form')->toHtml(),
      ));
      $this->addTab('form_member_credit', array(
            	'label'     => Mage::helper('affiliate')->__('Credit'),
            	'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_credit')->toHtml()
        	));
      $this->addTab('form_member_credit_history', array(
          'label'     => Mage::helper('affiliate')->__('Credit History'),
          'title'     => Mage::helper('affiliate')->__('Credit History'),
          'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_credithistory')->toHtml(),
     	  'active'    =>true,
     	   ));
      $this->addTab('form_member_invitation', array(
          'label'     => Mage::helper('affiliate')->__('Invitation History'),
          'title'     => Mage::helper('affiliate')->__('Invitation History'),
          'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_invitation')->toHtml(),
          ));
      $this->addTab('form_member_withdrawn', array(
          'label'     => Mage::helper('affiliate')->__('Withdrawal History'),
          'title'     => Mage::helper('affiliate')->__('Withdrawal History'),
          'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_withdrawn')->toHtml(),
     	  'active'    =>true,
     	   ));
      $this->addTab('form_member_transaction', array(
          'label'     => Mage::helper('affiliate')->__('Affiliate History'),
          'title'     => Mage::helper('affiliate')->__('Affiliate History'),
          'content'   => $content,
      	  'active'    =>true,
     	   ));
     
      return parent::_beforeToHtml();
  }
}