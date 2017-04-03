<?php
class TS_Reports_Block_List_Prices_Grid extends Mage_Adminhtml_Block_Widget_Grid {

	public function __construct(){
		parent::__construct();
		$this->setId('ts_reports_prices_grid');
		$this->setDefaultSort('order_item_id');
		$this->setDefasultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
	
	protected function _getCollectionClass(){
        return 'ts_reports/reportitem_collection';
    }

    protected function _prepareCollection(){
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);
		
		$categoriesCollection = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('*')->load()->toArray();
		if(function_exists( 'array_column' ) ) $categories = array_column($categoriesCollection, NULL, 'entity_id');
		else {
			$categories = array();
			foreach($categoriesCollection as $subArr){
				$categories[$subArr['entity_id']] = $subArr;
			}
		}
		
		Mage::getSingleton('core/session')->setTSReportsCategories( $categories );
        return parent::_prepareCollection();
    }

    public function _prepareMassaction() {
        $this->setMassactionIdField('order_item_id');
        $this->getMassactionBlock()->setFormFieldName('reportitem_id');
        $this->getMassactionBlock()->setUseSelectAll(true);
		
		parent::_prepareMassaction();
		$this->getMassactionBlock()->setUseSelectAll(true);
		
		
		$types = Mage::getModel('ts_reports/types');
		$types = $types::getTypeNames();
		
		foreach($types as $key => $type){
			$this->getMassactionBlock()->addItem('pricetype_'.$type, array(
				'label'	=> sprintf("%s '%s'", Mage::helper('ts_reports')->__('Set price as'), Mage::helper('ts_reports')->__($type)),
				'url'  	=> Mage::getUrl('ts_reports/prices/setPriceType/type/'.$key)
			));	
		}
		$this->getMassactionBlock()->addItem('pricetype_reset', array(
            'label'	=> Mage::helper('ts_reports')->__('Reset price override'),
            'url' 	=> Mage::getUrl('ts_reports/prices/setPriceType/')
        ));
    }
	
	protected function _prepareColumns(){
		$types = Mage::getModel('ts_reports/types');
		$types = $types::getTypeNames();
	
	
		$this->addColumn('order_item_id', array(
			'header'    => Mage::helper('ts_reports')->__('ID'),
			'index'     => 'order_item_id',
			'align'     => 'right',
			'width'     => '50px'
		));

		$this->addColumn('order_id', array(
			'header'    => Mage::helper('ts_reports')->__('Increment ID'),
			'index'     => 'increment_id',
			'align'     => 'left',
			'width'     => '100px',
			'renderer'  => 'TS_Reports_Block_Renderer_Orderid'
		));
		
		$this->addColumn('order_date', array(
			'header'    => Mage::helper('ts_reports')->__('Order Date'),
			'index'     => 'order_date',
			'align'     => 'left',
			'width'     => '150px',
			'type'		=> 'datetime'
		));
		
		$this->addColumn('sku', array(
			'header'    => Mage::helper('ts_reports')->__('SKU'),
			'index'     => 'sku',
			'align'     => 'right',
			'width'     => '100px',
			'renderer'  => 'TS_Reports_Block_Renderer_Productid'
		));

		$this->addColumn('name', array(
			'header'    => Mage::helper('ts_reports')->__('Product title'),
			'index'     => 'name',
			'align'     => 'left'
		));
		
		$this->addColumn('timezone', array(
			'header'    => Mage::helper('ts_reports')->__('Timezone'),
			'index'     => 'tz_offset',
			'align'     => 'right',
			'width'     => '100px',
			'filter' 	=> false,
			'renderer'	=> 'TS_Reports_Block_Renderer_Timezone'
		));
		$this->addColumn('price_type', array(
			'header'    => Mage::helper('ts_reports')->__('Price Type'),
			'index'     => 'price_type',
			'align'     => 'left',
			'width'     => '100px',
			'type'		=> 'options',
			'options'	=> $types,
			'renderer'	=> 'TS_Reports_Block_Renderer_Typename'
		));

		$this->addColumn('cart', array(
			'header'    => Mage::helper('ts_reports')->__('Cart discount'),
			'index'     => 'cart',
			'align'     => 'left',
			'width'     => '100px',
			'type'      => 'options',
			'options'   => array(
				1 => Mage::helper('ts_reports')->__('Yes'),
				0 => Mage::helper('ts_reports')->__('No'),
			  )
		));
		
		$this->addColumn('original_price', array(
			'header'    => Mage::helper('ts_reports')->__('Original price'),
			'index'     => 'original_price',
			'align'     => 'left',
			'width'     => '100px',
			'type'		=> 'currency',
			'currency'	=> 'base_currency_code'
		));
		
		$this->addColumn('base_price', array(
			'header'    => Mage::helper('ts_reports')->__('Sale price (excl. tax)'),
			'index'     => 'base_price',
			'align'     => 'left',
			'width'     => '100px',
			'type'		=> 'currency',
			'currency'	=> 'base_currency_code'
		));
		
		$this->addColumn('price_incl_tax', array(
			'header'    => Mage::helper('ts_reports')->__('Sale price (incl. tax)'),
			'index'     => 'price_incl_tax',
			'align'     => 'left',
			'width'     => '100px',
			'type'		=> 'currency',
			'currency'	=> 'base_currency_code'
		));
		
		$this->addColumn('categories', array(
			'header'    => Mage::helper('ts_reports')->__('Categories'),
			'index'     => 'categories',
			'align'     => 'left',
			'width'     => '300px',
			'renderer'  => 'TS_Reports_Block_Renderer_Categories'
		));
		
		return parent::_prepareColumns();
	}

	public function getGridUrl(){
		return $this->getUrl('*/*/grid', array('_current' => true));
	}
}
