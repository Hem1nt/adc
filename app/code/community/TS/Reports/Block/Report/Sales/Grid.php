<?php

class TS_Reports_Block_Report_Sales_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract {

    protected $_columnGroupBy = 'period';
	protected $_resourceCollectionName = ''; 
	protected $_resourceCollectionNameAggregated = 'ts_reports/order_aggregated_collection'; 
	protected $_resourceCollectionNameOrderItem = 'ts_reports/orderitem_collection';
	
    public function __construct(){
        parent::__construct();
        $this->setCountTotals(true);
    }
	
	public function getResourceCollection(){
        return $this->_resourceCollectionName;
    }

	public function getResourceCollection_Order(){
        return $this->_resourceCollectionName = $this->_resourceCollectionNameAggregated;
	}
	
	public function getResourceCollection_OrderItem(){
        return $this->_resourceCollectionName = $this->_resourceCollectionNameOrderItem;
	}
	
	
	protected function _getResourceCollection($filterData = null){
		if(!empty($filterData) && ($filterData->getData('price_from') || $filterData->getData('price_to') || $filterData->getData('price_types') || $filterData->getData('categories'))){
			$resourceName = $this->getResourceCollection_OrderItem();
		} else $resourceName = $this->getResourceCollection_Order();
		return Mage::getResourceModel($resourceName);
	}
	
    protected function _addCustomFilter($resourceCollection, $filterData){	 //??
		if(get_class($resourceCollection) == 'TS_Reports_Model_Resource_Orderitem_Collection'){
			$resourceCollection->addPriceRangeFilter($filterData->getData('price_from'), $filterData->getData('price_to'), $filterData->getData('excl_tax'));
			$resourceCollection->addPriceTypeFilter($filterData->getData('price_types'),  $filterData->getData('excl_pricetype'));
			$resourceCollection->addCategoryFilter($filterData->getData('categories'),  $filterData->getData('excl_cat'));
		}
        return $this;
    }
	
	

    public function getCountTotals(){
		if($this->getResourceCollectionName() != '') return parent::getCountTotals();
    }

    public function getSubTotals(){
		if($this->getResourceCollectionName() != '') return parent::getSubTotals();
    }
	
    protected function _prepareCollection(){
        $filterData = $this->getFilterData();

        if($filterData->getData('from') == null || $filterData->getData('to') == null) {
            $this->setCountTotals(false);
            $this->setCountSubTotals(false);
            return parent::_prepareCollection(); //?
        }

        $storeIds = $this->_getStoreIds();

        $orderStatuses = $filterData->getData('order_statuses');
        if(is_array($orderStatuses) && count($orderStatuses) == 1 && strpos($orderStatuses[0],',')!== false) {
			$filterData->setData('order_statuses', explode(',',$orderStatuses[0]));
        }

		$collection = $this->getCollection();
        $resourceCollection = $this->_getResourceCollection($filterData);
        //$resourceCollection = Mage::getResourceModel($this->getResourceCollection_Order())
		$resourceCollection = $resourceCollection
			->setPeriod($filterData->getData('period_type'))
            ->setDateRange($filterData->getData('from', null), $filterData->getData('to', null))
            ->addStoreFilter($storeIds)
            ->setAggregatedColumns($this->_getAggregatedColumns());
        $this->_addOrderStatusFilter($resourceCollection, $filterData);
        $this->_addCustomFilter($resourceCollection, $filterData);

        if($this->_isExport) {
            $this->setCollection($resourceCollection);
            return $this;
        }

        if($filterData->getData('show_empty_rows', false)) {
            Mage::helper('reports')->prepareIntervalsCollection(
                $this->getCollection(),
                $filterData->getData('from', null),
                $filterData->getData('to', null),
                $filterData->getData('period_type')
            );
        }

        if($this->getCountSubTotals()) $this->getSubTotals();
		
        if($this->getCountTotals()) {
            $totalsCollection = $this->_getResourceCollection($filterData)
                ->setPeriod($filterData->getData('period_type'))
                ->setDateRange($filterData->getData('from', null), $filterData->getData('to', null))
                ->addStoreFilter($storeIds)
                ->setAggregatedColumns($this->_getAggregatedColumns())
                ->isTotals(true);
            $this->_addOrderStatusFilter($totalsCollection, $filterData);
            $this->_addCustomFilter($totalsCollection, $filterData); //->vot siia tuleb ka customFilter ehitada!

            foreach ($totalsCollection as $item) {
                $this->setTotals($item);
                break;
            }
        }

        $this->getCollection()->setColumnGroupBy($this->_columnGroupBy); //see on erroriviskaja
		$this->getCollection()->setResourceCollection($resourceCollection);

        //return parent::_prepareCollection();
		return $this;
    }

	//public function addGrandTotals($total){}
	//public function getGrandTotals(){}
	//public function getCountTotals(){}
	
	protected function _prepareColumns(){
        $this->addColumn('period', array(
            'header'        => Mage::helper('sales')->__('Period'),
            'index'         => 'period',
            'width'         => 100,
            'sortable'      => false,
            'period_type'   => $this->getPeriodType(),
            'renderer'      => 'adminhtml/report_sales_grid_column_renderer_date',
            'totals_label'  => Mage::helper('sales')->__('Total'),
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('orders_count', array(
            'header'	=> Mage::helper('sales')->__('Orders'),
            'index'		=> 'orders_count',
            'type'		=> 'number',
            'total'		=> 'sum',
            'sortable'	=> false
        ));

        $this->addColumn('total_qty_ordered', array(
            'header'	=> Mage::helper('sales')->__('Sales Items'),
            'index'		=> 'total_qty_ordered',
            'type'		=> 'number',
            'total'		=> 'sum',
            'sortable'	=> false
        ));

        $this->addColumn('total_qty_invoiced', array(
            'header'	=> Mage::helper('sales')->__('Items'),
            'index'		=> 'total_qty_invoiced',
            'type'		=> 'number',
            'total'		=> 'sum',
            'sortable'	=> false,
            'visibility_filter' => array('show_actual_columns')
        ));

        if($this->getFilterData()->getStoreIds()) $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
		
        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);

        $this->addColumn('total_income_amount', array(
            'header'        => Mage::helper('sales')->__('Sales Total'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_income_amount',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ));

        $this->addColumn('total_revenue_amount', array(
            'header'            => Mage::helper('sales')->__('Revenue'),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'total_revenue_amount',
            'total'             => 'sum',
            'sortable'          => false,
            'visibility_filter' => array('show_actual_columns'),
            'rate'              => $rate,
        ));

        $this->addColumn('total_profit_amount', array(
            'header'            => Mage::helper('sales')->__('Profit'),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'total_profit_amount',
            'total'             => 'sum',
            'sortable'          => false,
            'visibility_filter' => array('show_actual_columns'),
            'rate'              => $rate,
        ));

        $this->addColumn('total_invoiced_amount', array(
            'header'        => Mage::helper('sales')->__('Invoiced'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_invoiced_amount',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ));

        $this->addColumn('total_paid_amount', array(
            'header'            => Mage::helper('sales')->__('Paid'),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'total_paid_amount',
            'total'             => 'sum',
            'sortable'          => false,
            'visibility_filter' => array('show_actual_columns'),
            'rate'              => $rate,
        ));

        $this->addColumn('total_refunded_amount', array(
            'header'        => Mage::helper('sales')->__('Refunded'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_refunded_amount',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ));

        $this->addColumn('total_tax_amount', array(
            'header'        => Mage::helper('sales')->__('Sales Tax'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_tax_amount',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ));

        $this->addColumn('total_tax_amount_actual', array(
            'header'            => Mage::helper('sales')->__('Tax'),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'total_tax_amount_actual',
            'total'             => 'sum',
            'sortable'          => false,
            'visibility_filter' => array('show_actual_columns'),
            'rate'              => $rate,
        ));

        $this->addColumn('total_shipping_amount', array(
            'header'        => Mage::helper('sales')->__('Sales Shipping'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_shipping_amount',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ));

        $this->addColumn('total_shipping_amount_actual', array(
            'header'            => Mage::helper('sales')->__('Shipping'),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'total_shipping_amount_actual',
            'total'             => 'sum',
            'sortable'          => false,
            'visibility_filter' => array('show_actual_columns'),
            'rate'              => $rate,
        ));

        $this->addColumn('total_discount_amount', array(
            'header'        => Mage::helper('sales')->__('Sales Discount'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_discount_amount',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ));

        $this->addColumn('total_discount_amount_actual', array(
            'header'            => Mage::helper('sales')->__('Discount'),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'total_discount_amount_actual',
            'total'             => 'sum',
            'sortable'          => false,
            'visibility_filter' => array('show_actual_columns'),
            'rate'              => $rate,
        ));

        $this->addColumn('total_canceled_amount', array(
            'header'        => Mage::helper('sales')->__('Canceled'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_canceled_amount',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ));

        $this->addExportType('*/*/exportSalesCsv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportSalesExcel', Mage::helper('adminhtml')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
