<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml customer grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class MW_Affiliate_Block_Adminhtml_Affiliatemember_Edit_Tab_Invitation extends Mage_Adminhtml_Block_Widget_Grid
{

 public function __construct()
    {
        parent::__construct();
        $this->setId('Affiliate_member_invitation');
       // $this->setDefaultSort('transaction_time');
       // $this->setDefaultDir('desc');

        $this->setUseAjax(true);
        $this->setEmptyText(Mage::helper('affiliate')->__('No Invitation History Found'));
    }
public function getGridUrl()
    {
    return $this->getUrl('affiliate/adminhtml_affiliatemember/invitation', array('id'=>$this->getRequest()->getParam('id')));
        
    }
	
protected function _prepareCollection()
  {
      $collection = Mage::getModel('affiliate/affiliateinvitation')->getCollection()
      				->addFieldToFilter('customer_id',$this->getRequest()->getParam('id'))
      				->setOrder('invitation_time', 'DESC');
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('invitation_id', array(
          'header'    => Mage::helper('affiliate')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'invitation_id',
      ));

      $this->addColumn('invitation_time', array(
			'header'    => Mage::helper('affiliate')->__('Inviation Time'),
			'width'     => '150px',
			'index'     => 'invitation_time',
      ));
	  $this->addColumn('email', array(
          'header'    => Mage::helper('affiliate')->__('Email'),
          'align'     =>'left',
          'index'     => 'email',
      ));
      $this->addColumn('ip', array(
          'header'    => Mage::helper('affiliate')->__('Ip Address'),
          'align'     =>'left',
          'index'     => 'ip',
      ));
      $this->addColumn('status', array(
          	'header'    => Mage::helper('affiliate')->__('Status'),
          	'align'     =>'center',
          	'index'     => 'status',
		  	'type'      => 'options',
          	'options'   => Mage::getSingleton('affiliate/statusinvitation')->getOptionArray(),
      	));
	  
      return parent::_prepareColumns();
  }
	
   
}
