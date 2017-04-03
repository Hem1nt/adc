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
class MW_Affiliate_Block_Adminhtml_Affiliatemember_Edit_Tab_Transaction extends Mage_Adminhtml_Block_Widget_Grid
{

 	public function __construct()
    {
        parent::__construct();
        $this->setId('Affiliate_member_transaction');
       // $this->setDefaultSort('transaction_time');
       // $this->setDefaultDir('desc');

        $this->setUseAjax(true);
        $this->setEmptyText(Mage::helper('affiliate')->__('No Affiliate Transaction Found'));
    }
	public function getGridUrl()
    {
    	return $this->getUrl('affiliate/adminhtml_affiliatemember/transaction', array('id'=>$this->getRequest()->getParam('id')));
        
    }
	protected function _prepareCollection()
  	{
      	$collection = Mage::getModel('affiliate/affiliatetransaction')->getCollection()
      					->addFieldToFilter('customer_invited',$this->getRequest()->getParam('id'))
						->setOrder('transaction_time', 'DESC')
						->setOrder('history_id', 'DESC');
      	$this->setCollection($collection);
      	return parent::_prepareCollection();
  	}
  	protected function _prepareColumns()
  	{
  		//$this->setTemplate('mw_credit/gridtransaction.phtml');
        $this->addColumn('history_id', array(
            'header'    =>  Mage::helper('affiliate')->__('ID'),
            'align'     =>  'left',
            'index'     =>  'history_id',
            'width'     =>  10
        ));
        
      	$this->addColumn('transaction_time', array(
            'header'    =>  Mage::helper('affiliate')->__('Transaction Time'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'index'     =>  'transaction_time',
            'gmtoffset' => true,
            'default'   =>  ' ---- '
        ));
	  
        $this->addColumn('order_id', array(
            'header'    =>  Mage::helper('affiliate')->__('Order Number'),
            'align'     =>  'left',
            'index'     =>  'order_id',
        	//'renderer'  => 'affiliate/adminhtml_renderer_orderid',
        ));
      	
      	$this->addColumn('total_commission', array(
          	'header'    => Mage::helper('affiliate')->__('Commission'),
          	'index'     => 'total_commission',
      		'type'      =>  'price',
            'currency_code' => Mage::app()->getBaseCurrencyCode(),
      	));
	  
      
		$this->addColumn('total_discount', array(
            'header'    =>  Mage::helper('affiliate')->__('Customer Discount'),
        	'align'     =>  'center',
            'index'     =>  'total_discount',
			'type'      =>  'price',
            'currency_code' => Mage::app()->getBaseCurrencyCode(),
        ));
        
        $this->addColumn('status', array(
          	'header'    => Mage::helper('affiliate')->__('Status'),
          	'align'     =>'center',
          	'index'     => 'status',
		  	'type'      => 'options',
          	'options'   => Mage::getSingleton('affiliate/status')->getOptionArray(),
      	));
      	$this->addColumn('action',
            array(
                'header'    =>  Mage::helper('affiliate')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getOrderId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('affiliate')->__('View'),
                        'url'       => array('base'=> '*/*/*/id/'.$this->getRequest()->getParam('id')),
                        'field'     => 'orderid'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
//		$this->addExportType('*/*/exportCsv', Mage::helper('affiliate')->__('CSV'));
//		$this->addExportType('*/*/exportXml', Mage::helper('affiliate')->__('XML'));
	  
      	return parent::_prepareColumns();
  	}
	public function getRowUrl($row)
  	{
		return $this->getUrl('*/*/*/id/'.$this->getRequest()->getParam('id'), array('orderid' => $row->getOrderId()));
  	}
}
