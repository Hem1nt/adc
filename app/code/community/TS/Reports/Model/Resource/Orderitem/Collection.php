<?php

class TS_Reports_Model_Resource_Orderitem_Collection extends Mage_Sales_Model_Resource_Report_Order_Collection {

    protected $_isProducts	= false;

	protected $_priceType 	= null;
	protected $_exclPriceType = false;
	
	protected $_priceFrom 	= null;
	protected $_priceTo 	= null;
	protected $_exclTax 	= false;
	
	protected $_category 	= null;
	protected $_exclCat 	= false;
		
	protected $_showPrices	= false;
	protected $_showActual	= false;
	
	
    protected $_aggregationTable = 'ts_reports/orderitem';	
	
 	protected function _construct(){
		$this->_init('ts_reports/orderitem');
	}

    public function __construct(){
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init($this->_aggregationTable, 'order_item_id');
        $this->setConnection($this->getResource()->getReadConnection());
    }

	
	/**
     * Load from Collection by Order Item ID
     *
     * @param string $item
     * @return TS_Reports_Model_Resource_Orderitem_Collection
     */
	public function loadByOrderItemId($item){
		return $this->addFieldToFilter('order_item', array('eq' => $item));
	}
	
	/**
     * Set if list of products
     *
     * @param boolean $flag
     * @return TS_Reports_Model_Resource_Orderitem_Collection
     */
    public function isProducts($flag = null){
        if(is_null($flag)) return $this->_isProducts;
        $this->_isProducts = $flag;
        return $this;
    }
	
	
	/**
     * Set price range filter
     *
     * @param string $priceFrom
     * @param string $priceTo
     * @param boolean $exclTax
     * @return TS_Reports_Model_Resource_Orderitem_Collection
     */
	public function addPriceRangeFilter($priceFrom = null, $priceTo = null, $exclTax = false){
		$this->_priceFrom 	= $priceFrom;
		$this->_priceTo 	= $priceTo;
		$this->_exclTax 	= $exclTax;
		return $this;	
	}
	
    /**
     * Set price type filter
     *
     * @param string|array $priceType
     * @param boolean $exclPriceType
     * @return TS_Reports_Model_Resource_Orderitem_Collection
     */
	public function addPriceTypeFilter($priceType, $exclPriceType = false){
		$this->_priceType = $priceType;
		$this->_exclPriceType = $exclPriceType;
		return $this;	
	}
	
    /**
     * Set category filter
     *
     * @param string $category
     * @param boolean $exclCat
     * @return TS_Reports_Model_Resource_Orderitem_Collection
     */
	public function addCategoryFilter($category = null, $exclCat = false){
		if(!$category) return;
        $this->_category = $category;
        $this->_exclCat = $exclCat;
        return $this;
    }
	
    /**
     * Set show prices filter
     *
     * @param string $showPrices
     * @return TS_Reports_Model_Resource_Orderitem_Collection
     */
	public function addShowPricesFilter($showPrices){
        $this->_showPrices = $showPrices;
        return $this;
    }
	
	/**
     * Set if show actual (for products to add a WHERE clause)
     *
     * @param string $showActual
     * @return TS_Reports_Model_Resource_Orderitem_Collection
     */
    public function addShowActualFilter($showActual = false){
        $this->_showActual = $showActual;
        return $this;
    }
	
	
	/**
     * Get selected columns?
     *
     * @return array
     */
    protected function _getSelectedColumns(){
        $adapter = $this->getConnection();
		
        if('year' == $this->_period) 		$this->_periodFormat = $adapter->getDateExtractSql('period', Varien_Db_Adapter_Interface::INTERVAL_YEAR);
        elseif('month' == $this->_period) 	$this->_periodFormat = $adapter->getDateFormatSql('period', '%Y-%m');
        else 								$this->_periodFormat = $adapter->getDateFormatSql('period', '%Y-%m-%d');

		if($this->isTotals()) $this->_selectedColumns = $this->getAggregatedColumns();
		else if($this->isProducts()){
			$this->_selectedColumns = array(
                'period'					=> $this->_periodFormat,
                'name'						=> 'ri.name',
                'sku'						=> 'ri.sku',
                'price_type'				=> 'ri.price_type',
                'base_price'				=> 'ri.base_price',
                'price_incl_tax'			=> 'ri.price_incl_tax',
                'total_qty_ordered'			=> 'SUM(total_qty_ordered)',
                'total_qty_ordered_actual'	=> 'SUM(total_qty_ordered_actual)',
                'total_qty_invoiced'		=> 'SUM(total_qty_invoiced)',
                'total_qty_invoiced_actual'	=> 'SUM(total_qty_invoiced_actual)',
            );
		} else {
            $this->_selectedColumns = array(
                'period'                        => $this->_periodFormat,
                'orders_count'                  => 'SUM(orders_count)',
                'total_qty_ordered'             => 'SUM(total_qty_ordered)',
                'total_qty_ordered_actual'		=> 'SUM(total_qty_ordered_actual)',
                'total_qty_invoiced'            => 'SUM(total_qty_invoiced)',
                'total_qty_invoiced_actual'		=> 'SUM(total_qty_invoiced_actual)',
                'total_income_amount'           => 'SUM(total_income_amount)',
                'total_revenue_amount'          => 'SUM(total_revenue_amount)',
                'total_profit_amount'           => 'SUM(total_profit_amount)',
                'total_invoiced_amount'         => 'SUM(total_invoiced_amount)',
                'total_canceled_amount'         => 'SUM(total_canceled_amount)',
                'total_paid_amount'             => 'SUM(total_paid_amount)',
                'total_refunded_amount'         => 'SUM(total_refunded_amount)',
                'total_tax_amount'              => 'SUM(total_tax_amount)',
                'total_tax_amount_actual'       => 'SUM(total_tax_amount_actual)',
                'total_shipping_amount'         => 'SUM(total_shipping_amount)',
                'total_shipping_amount_actual'  => 'SUM(total_shipping_amount_actual)',
                'total_discount_amount'         => 'SUM(total_discount_amount)',
                'total_discount_amount_actual'  => 'SUM(total_discount_amount_actual)',
            );
        }

        return $this->_selectedColumns;
    }

    protected function _initSelect(){
		parent::_initSelect();	
		$subselect = $this->getConnection()->select()->from($this->getResource()->getMainTable(), array(
				'*',
				'orders_count' 	=> new Zend_Db_Expr('0')
			));
		
		$select = $this->getSelect()->reset()->from(array('oi' => $subselect), $this->_getSelectedColumns() )
					->join(array('ri' => $this->getTable('ts_reports/reportitem')), 'ri.order_item_id = oi.order_item_id', array());

        if(!$this->isTotals()){	
            $this->getSelect()->group($this->_periodFormat);
			if($this->isProducts()){
				$this->getSelect()->group('sku')->order('period')->order('total_qty_ordered DESC');
			}
        }		
       
    }
	
    /**
     * Apply custom filters
     *
     * @return void
     */
	protected function _applyCustomFilter(){
		parent::_applyCustomFilter();
		$this->_applyPriceRangeFilter();
		$this->_applyPriceTypeFilter();
		$this->_applyCategoryFilter();
		$this->_applyShowPrices();
		//$this->_applyShowActualFilter();
	}
	
    
	/**
     * Apply show actual columns filter
     *
     * @param boolean $flag
     * @return TS_Reports_Model_Resource_Orderitem_Collection
     */
    public function _applyShowActualFilter($flag = null){
		if(!$this->isTotals() && $this->_isProducts && $this->_showActual){
			$this->getSelect()->where('total_qty_ordered_actual > 0');
Mage::log("here",null,"t.log");
		}
    }
	
    /**
     * Apply price range filter
	 *
     * @return void
     */
	protected function _applyPriceRangeFilter(){
		$priceColumn = 'price_incl_tax';
		if($this->_exclTax) $priceColumn = 'base_price';
		if($this->_priceFrom) 	$this->getSelect()->where('ri.'.$priceColumn.' >= ?', $this->_priceFrom);
		if($this->_priceTo) 	$this->getSelect()->where('ri.'.$priceColumn.' <= ?', $this->_priceTo);
	}
	
    /**
     * Apply price type filter
	 *
     * @return void
     */
	protected function _applyPriceTypeFilter(){
		$NOT = '';
		if($this->_priceType){
			if(!is_array($this->_priceType)) $this->_priceType = array($this->_priceType);
			if($this->_exclPriceType) $NOT = 'NOT';
			$this->getSelect()->where('ri.price_type '.$NOT.' IN (?)', $this->_priceType);
		}
	}
	
    /**
     * Apply category filter
	 *
     * @return void
     */
	protected function _applyCategoryFilter(){
		$NOT = '';
		if($this->_exclCat) $NOT = 'NOT';
		if($this->_category){	
			if(is_array($this->_category) && isset($this->_category[0])){				
				$this->_category = explode(',', $this->_category[0]);
			}
			$this->getSelect()->where('ri.categories '.$NOT.' REGEXP ?', implode('|',Mage::helper('ts_reports')->sanitize($this->_category)) ); 
		} 	// REGEXP *is* slower, but considering our list of categories, it is more versatile
	}
	
    /**
     * Show products prices
	 *
     * @return void
     */
	protected function _applyShowPrices(){
		if(!$this->isTotals() && $this->_isProducts && $this->_showPrices){
            $this->getSelect()->group('base_price');
			$this->getSelect()->group('price_incl_tax'); 
		}
	}
	
	
}
	
