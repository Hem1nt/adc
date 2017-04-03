<?php
class MW_Affiliate_Block_Adminhtml_Affiliatewithdrawn_Grid extends Mage_Adminhtml_Block_Widget_Grid
{ 
	public function __construct()
    {
        parent::__construct();
        $this->setId('affiliate_withdrawn');
        //$this->setDefaultSort('withdraw_time');
        //$this->setDefaultDir('desc');

        //$this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setEmptyText(Mage::helper('affiliate')->__('No Withdrawal History'));
    }
	 protected function _prepareCollection()
    {   
    	//$customer = Mage::getModel('customer/customer')->getCollection();
    	$resource = Mage::getModel('core/resource');
  	  	$table_customer = $resource->getTableName('customer/entity');
  	  	
    	$affiliate_customer = Mage::getModel('affiliate/affiliatebanner')->getCollection();
  		$customer_table = $affiliate_customer->getTable('affiliatecustomers');
        $collection = Mage::getResourceModel('affiliate/affiliatewithdrawn_collection')
						->setOrder('withdrawn_time', 'DESC')
						->setOrder('withdrawn_id', 'DESC');
		$collection->getSelect()->join(
      							array('customer_entity'=>$table_customer),'main_table.customer_id = customer_entity.entity_id',array('email'));				
       /* $collection->getSelect()->join(
        	array('mw_affiliate_customers'=>$customer_table),'main_table.customer_id = mw_affiliate_customers.customer_id',
        	array('mw_affiliate_customers.payment_email','mw_affiliate_customers.payment_gateway'));*/
        	
        //echo $collection->getSelect();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {   
    	//$a=$this->$row->getId();
    	//$credit = Mage::helper('credit')->getCreditByCustomer(); 
        $this->addColumn('withdrawn_id', array(
            'header'    =>  Mage::helper('affiliate')->__('ID'),
            'align'     =>  'left',
            'index'     =>  'withdrawn_id',
        	'name'		=>  'withdrawn_id',
            'width'     =>  15
        ));
		$this->addColumn('email', array(
          	'header'    => Mage::helper('affiliate')->__('Referral Email'),
          	'align'     =>'left',
          	'index'     => 'email',
		  	'width'     => '250px',
		  	'type'      => 'text',
      		'renderer'  => 'affiliate/adminhtml_renderer_emailaffiliatemember',
      	));
        $this->addColumn('payment_gateway', array(
            'header'    =>  Mage::helper('affiliate')->__('Payment Method'),
        	'align'     =>  'left',
            'index'     =>  'payment_gateway',
        	'type'      => 'options',
            'options'   => $this ->_getPaymentGatewayArray()
        	//'options'   => MW_Affiliate_Model_Gateway::getOptionArray()
        ));
        $this->addColumn('payment_email', array(
            'header'    =>  Mage::helper('affiliate')->__('Payment Email'),
        	'align'     =>  'left',
            'index'     =>  'payment_email',
        	'type'      => 'text',
        ));

      	$this->addColumn('withdrawn_time', array(
            'header'    =>  Mage::helper('affiliate')->__('Withdrawal Time'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'index'     =>  'withdrawn_time',
            //'gmtoffset' => true,
        ));
        $this->addColumn('withdrawn_amount', array(
            'header'    =>  Mage::helper('affiliate')->__('Withdrawal Amount'),
        	'align'     =>  'left',
            'type'      =>  'price',
            'index'     =>  'withdrawn_amount',
        	'currency_code' => Mage::app()->getBaseCurrencyCode(),
        ));
        $this->addColumn('fee', array(
            'header'    =>  Mage::helper('affiliate')->__('Payment Processing Fee'),
        	'align'     =>  'left',
            'type'      =>  'price',
            'index'     =>  'fee',
        	'currency_code' => Mage::app()->getBaseCurrencyCode(),
        ));
        $this->addColumn('amount_receive', array(
            'header'    =>  Mage::helper('affiliate')->__('Net Amount'),
        	'align'     =>  'center',
            'type'      =>  'price',
            'index'     =>  'amount_receive',
        	'currency_code' => Mage::app()->getBaseCurrencyCode(),
        ));
         $this->addColumn('status', array(
            'header'    =>  Mage::helper('affiliate')->__('Status'),
            'align'     =>  'center',
            'index'     =>  'status',
         	'type'      => 'options',
          	'options'   => MW_Affiliate_Model_Status::getOptionArray(),
         	'width'     =>  100
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('affiliate')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('affiliate')->__('XML'));
         return parent::_prepareColumns();
    }
  private function _getPaymentGatewayArray()
    {
    	$arr = array();
    	$gateways = unserialize(Mage::helper('affiliate/data')->getGatewayStore());
		foreach ($gateways as $gateway) 
		{
			$arr[$gateway['gateway_value']] =  $gateway['gateway_title'];
		}
		return $arr;
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