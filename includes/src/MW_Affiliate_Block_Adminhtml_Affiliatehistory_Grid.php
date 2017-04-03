<?php
class MW_Affiliate_Block_Adminhtml_Affiliatehistory_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	
  	public function __construct()
  	{
      	parent::__construct();
      	$this->setId('affiliate_history');
      	//$this->setUseAjax(true);
      	//$this->setDefaultSort('created_time');
      	//$this->setDefaultDir('DESC');
      	$this->setSaveParametersInSession(true);
      	$this->setEmptyText(Mage::helper('affiliate')->__('No Affiliate History Found'));
  	}
  	
    /**
     * Transaction Detail
     * 
     * @return array
     */
    // trong 1 order co the co nhieu san pham
    // moi san pham co nguoi invited khac nhau
  	protected function _prepareCollection()
  	{
  		$resource = Mage::getModel('core/resource');
  	  	$customer_table = $resource->getTableName('customer/entity');
      	$collection = Mage::getModel('affiliate/affiliatetransaction')->getCollection()
      					->addFieldtoFilter('customer_invited',0)
						->setOrder('transaction_time', 'DESC')
						->setOrder('history_id', 'DESC');
		$collection->getSelect()->joinLeft(
      							array('customer_entity'=>$customer_table),'main_table.show_customer_invited = customer_entity.entity_id',array('email'));
						
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
            'width'     =>  20
        ));
        
      	$this->addColumn('transaction_time', array(
            'header'    =>  Mage::helper('affiliate')->__('Transaction Time'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'index'     =>  'transaction_time',
      		'width'		=>  250,
            'gmtoffset' => true,
            'default'   =>  ' ---- '
        ));

        $this->addColumn('order_id', array(
            'header'    =>  Mage::helper('affiliate')->__('Order Number'),
            'align'     =>  'left',
        	'width'		=>  150,
            'index'     =>  'order_id',
        	'type'      => 'text',
      		//'renderer'  => 'affiliate/adminhtml_renderer_orderid',
        ));
        $this->addColumn('email', array(
          'header'    => Mage::helper('affiliate')->__('Affiliate Account'),
          'align'     =>'left',
          'index'     => 'email',
      	  //'renderer'  => 'affiliate/adminhtml_renderer_showcustomerinvited',
      ));
    	
      	$this->addColumn('total_commission', array(
          	'header'    => Mage::helper('affiliate')->__('Commission'),
          	'index'     => 'total_commission',
      		'width'		=>  200,
      		'type'      =>  'price',
            'currency_code' => Mage::app()->getBaseCurrencyCode(),
      	));
	  
      
		$this->addColumn('total_discount', array(
            'header'    =>  Mage::helper('affiliate')->__('Customer Discount'),
        	'align'     =>  'center',
			'width'		=>  200,
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
                        'url'       => array('base'=> '*/adminhtml_affiliateviewhistory/'),
                        'field'     => 'orderid'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('affiliate')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('affiliate')->__('XML'));
	  
      	return parent::_prepareColumns();
  	}
  	
	protected function _prepareMassaction()
    {
        $this->setMassactionIdField('affiliate_history');
        $this->getMassactionBlock()->setFormFieldName('mw_history_id');

        $statuses = MW_Affiliate_Model_Status::getOptionAction();

        //array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('catalog')->__('Change status'),
             'url'  => $this->getUrl('affiliate/adminhtml_affiliatehistory/updateStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('catalog')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  	public function getRowUrl($row)
  	{
		return $this->getUrl('*/adminhtml_affiliateviewhistory/', array('orderid' => $row->getOrderId()));
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
                    
                    if($col_id == 'order_id')
                    {   
                    	$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $item->getOrderId()).'"';
                    	//zend_debug::dump($item->getOrderId());die();
                    }
                    else
                    {
                    	$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $column->getRowFieldExport($item)).'"';
                    }
                    
                }
            }
            $csv.= implode(',', $data)."\n";
            //zend_debug::dump($data);die();
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