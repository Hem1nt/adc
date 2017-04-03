<?php

class MW_Affiliate_Block_Adminhtml_Affiliateparent_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('customerGrid');
      $this->setDefaultSort('customer_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
      $this->setEmptyText(Mage::helper('affiliate')->__('No Customer Found'));
  }
  protected function _prepareCollection()
  {   
  	  $resource = Mage::getModel('core/resource');
  	  $collection_banner = Mage::getModel('affiliate/affiliatebanner')->getCollection();
  	  $customer_table = $collection_banner->getTable('affiliatecustomers');
  	  $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email');
       $collection->getSelect()->joinLeft(
      							array(
      							'customer_affiliate'=>$customer_table),
      							'e.entity_id = customer_affiliate.customer_id',
      							array('customer_invited')
      						);
           
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }
  protected function _prepareColumns()
  {   
      $this->addColumn('entity_id', array(
            'header'    => Mage::helper('customer')->__('ID'),
            'width'     => '50px',
            'index'     => 'entity_id',
            'type'  => 'number',
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('customer')->__('Customer Name'),
            'index'     => 'name'
        ));
       $this->addColumn('email', array(
            'header'    => Mage::helper('customer')->__('Customer Email'),
            'index'     => 'email'
        ));
      $this->addColumn('customer_invited', array(
          'header'    => Mage::helper('affiliate')->__('Affiliate Parent'),
          //'type'      =>'number',
          'align'     =>'left',
          'index'     => 'customer_invited',
          'filter_condition_callback' => array($this, '_filterCustomerInvitedCondition'),
      	  'renderer'  => 'affiliate/adminhtml_renderer_customerinvited',
      ));
      $this->addColumn('referral_name', array(
          'header'    => Mage::helper('affiliate')->__('Referral Name'),
          'align'     =>'left',
          'index'     => 'customer_invited',
      	  'filter_condition_callback' => array($this, '_filterReferralnameCondition'),
      	  'renderer'  => 'affiliate/adminhtml_renderer_parentname',
      ));
  
	  $this->addExportType('*/*/exportCsv', Mage::helper('affiliate')->__('CSV'));
	  $this->addExportType('*/*/exportXml', Mage::helper('affiliate')->__('XML'));
	  
      return parent::_prepareColumns();
  }
	protected function _filterReferralnameCondition($collection, $column)
    {
       if (!$value = $column->getFilter()->getValue()) {
            return;
        }
       $customer_ids = array();
       $value = '%'.$value.'%';
      // $customer_collections =  Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('firstname',array('like' => $value));
       $customer_collections =  Mage::getModel('customer/customer')->getCollection()
       		->addAttributeToFilter(array(
		    array(
		        'attribute' => 'firstname',
		        array('like' => $value),
		        ),
		    array(
		        'attribute' => 'lastname',
		        array('like' => $value),
		        ),
		    ));
       foreach ($customer_collections as $customer_collection) {
       		$customer_ids[] = $customer_collection->getId();
       }
       $this->getCollection()->getSelect()->where("customer_affiliate.customer_invited in (?)",$customer_ids);
    }
	protected function _filterCustomerInvitedCondition($collection, $column)
    {
       if (!$value = $column->getFilter()->getValue()) {
            return;
        }
       $customer_ids = array();
       $value = '%'.$value.'%';
       $customer_collections =  Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('email',array('like' => $value));
       foreach ($customer_collections as $customer_collection) {
       		$customer_ids[] = $customer_collection->getId();
       }
       $this->getCollection()->getSelect()->where("customer_affiliate.customer_invited in (?)",$customer_ids);
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('customerGrid');

        $this->getMassactionBlock()->addItem('parent_affiliate', array(
             'label'=> Mage::helper('affiliate')->__('Change Affiliate Parent'),
             'url'  => $this->getUrl('*/*/massParentAffiliate', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'parent_affiliate',
                         'type' => 'text',
                         'class' => 'required-entry validate-email',
                         'label' => Mage::helper('affiliate')->__('Affiliate Parent'),
                     )
             )
        ));
        return $this;
    }

  public function getCsv()
    {
        $csv = '';
        $this->_isExport = true;
        $this->_prepareGrid();
        $this->getCollection()->getSelect()->limit();
        $this->getCollection()->setPageSize(0);
        $this->getCollection()->load();
        $this->_afterLoadCollection();

        $data = array();
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $data[] = '"'.$column->getExportHeader().'"';
            }
        }
        $csv.= implode(',', $data)."\n";

        foreach ($this->getCollection() as $item) {
            $data = array();
            foreach ($this->_columns as $col_id =>$column) {
                if (!$column->getIsSystem()) {
                	if($col_id == 'email')
                    {   
                    	$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $item->getEmail()).'"';
                    	//zend_debug::dump($item->getOrderId());die();
                    }
                    else
                    {
                    	$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $column->getRowFieldExport($item)).'"';
                    }
                }
            }
            $csv.= implode(',', $data)."\n";
        }

        if ($this->getCountTotals())
        {
            $data = array();
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem()) {
                    $data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $column->getRowFieldExport($this->getTotals())).'"';
                }
            }
            $csv.= implode(',', $data)."\n";
        }

        return $csv;
    }

}