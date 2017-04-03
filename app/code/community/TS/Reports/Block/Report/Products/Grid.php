<?php

class TS_Reports_Block_Report_Products_Grid extends TS_Reports_Block_Report_Sales_Grid {
//Mage_Adminhtml_Block_Report_Grid_Abstract {

    protected $_columnGroupBy = 'period';
	protected $_resourceCollectionName = 'ts_reports/orderitem_collection';
	
    public function __construct(){
        parent::__construct();
        $this->setCountTotals(true);
    }
	
	public function getResourceCollectionName(){
        return $this->_resourceCollectionName;
    }
	
    protected function _addCustomFilter($resourceCollection, $filterData){
		parent::_addCustomFilter($resourceCollection, $filterData);
		$resourceCollection->addShowPricesFilter($filterData->getData('show_prices'));
		$resourceCollection->addShowActualFilter($filterData->getData('show_actual_columns'));
        return $this;
    }
	
    protected function _prepareCollection(){
        $filterData = $this->getFilterData();

        if($filterData->getData('from') == null || $filterData->getData('to') == null) {
            $this->setCountTotals(false);
            $this->setCountSubTotals(false);
            return parent::_prepareCollection();
        }

        $storeIds = $this->_getStoreIds();

        $orderStatuses = $filterData->getData('order_statuses');
        if(is_array($orderStatuses) && count($orderStatuses) == 1 && strpos($orderStatuses[0],',')!== false) {
			$filterData->setData('order_statuses', explode(',',$orderStatuses[0]));
        }

		$collection = $this->getCollection();
        $resourceCollection = Mage::getResourceModel($this->getResourceCollectionName())
			->setPeriod($filterData->getData('period_type'))
            ->setDateRange($filterData->getData('from', null), $filterData->getData('to', null))
            ->addStoreFilter($storeIds)
            ->setAggregatedColumns($this->_getAggregatedColumns())
			->isProducts(true);
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
		
        if($this->getCountTotals()){
            $totalsCollection = Mage::getResourceModel($this->getResourceCollectionName())
                ->setPeriod($filterData->getData('period_type'))
                ->setDateRange($filterData->getData('from', null), $filterData->getData('to', null))
                ->addStoreFilter($storeIds)
                ->setAggregatedColumns($this->_getAggregatedColumns())
                ->isProducts(true)
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

	protected function _prepareColumns(){
        $this->addColumn('period', array(
            'header'        => Mage::helper('sales')->__('Period'),
            'index'         => 'period',
            'width'         => '50px',
            'sortable'      => false,
            'period_type'   => $this->getPeriodType(),
            'renderer'      => 'adminhtml/report_sales_grid_column_renderer_date',
            'totals_label'  => Mage::helper('sales')->__('Total'),
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('sku', array(
            'header'    	=> Mage::helper('sales')->__('SKU'),
            'index'     	=> 'sku',
            'width'         => '100px',
            'type'      	=> 'text',
            'sortable'  	=> false
        ));

        $this->addColumn('name', array(
            'header'    	=> Mage::helper('sales')->__('Product Title'),
            'index'     	=> 'name',
            'type'      	=> 'text',
            'sortable'  	=> false
        ));

		
		$types = Mage::getModel('ts_reports/types');
		$types = $types::getTypeNames();
		
		$currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);
		
        $this->addColumn('price_type', array(
            'header'    		=> Mage::helper('sales')->__('Price Type'),
			'index'     		=> 'price_type',
			'align'   			=> 'left',
			'width'     		=> '100px',
			'type'				=> 'options',
			'options'			=> $types,
            'sortable'  		=> false,
            'visibility_filter' => array('show_prices'),
			'renderer'			=> 'TS_Reports_Block_Renderer_Typename'
        ));
		
        $this->addColumn('base_price', array(
            'header'    	=> Mage::helper('sales')->__('Price Excl. Tax'),
			'index'     	=> 'base_price',
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'sortable'  	=> false,
            'visibility_filter' => array('show_prices')
        ));

        $this->addColumn('price_incl_tax', array(
            'header'    	=> Mage::helper('sales')->__('Price Incl. Tax'),
			'index'     	=> 'price_incl_tax',
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'sortable'  	=> false,
            'visibility_filter' => array('show_prices')
        ));

        $this->addColumn('total_qty_ordered', array(
            'header'        => Mage::helper('sales')->__('Qty Ordered'),
            'index'         => 'total_qty_ordered',
            'type'          => 'number',
            'total'         => 'sum',
            'sortable'      => false
        ));
		
        $this->addColumn('total_qty_ordered_actual', array(
            'header'        => Mage::helper('sales')->__('Qty Ordered (Actual)'),
            'index'         => 'total_qty_ordered_actual',
            'type'          => 'number',
            'total'         => 'sum',
            'sortable'      => false,
            'visibility_filter' => array('show_actual_columns')
        ));
		
		
        $this->addColumn('total_qty_invoiced', array(
            'header'        => Mage::helper('sales')->__('Qty Invoiced'),
            'index'         => 'total_qty_invoiced',
            'type'          => 'number',
            'total'         => 'sum',
            'sortable'      => false
        ));
        $this->addColumn('total_qty_invoiced_actual', array(
            'header'        => Mage::helper('sales')->__('Qty Invoiced (Actual)'),
            'index'         => 'total_qty_invoiced_actual',
            'type'          => 'number',
            'total'         => 'sum',
            'sortable'      => false,
            'visibility_filter' => array('show_actual_columns')
        ));
		
		
        if($this->getFilterData()->getStoreIds()) $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
       
        $this->addExportType('*/*/exportProductsCsv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportProductsExcel', Mage::helper('adminhtml')->__('Excel XML'));

        return $this;
    }
}
