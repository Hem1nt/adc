<?php
class TS_Reports_Block_List_Pricesmini_Grid extends Mage_Adminhtml_Block_Widget_Grid {

	public function __construct(){
		parent::__construct();
		$this->setId('ts_reports_prices_mini_grid');
		$this->setDefaultSort('sku');
		$this->setDefasultDir('DESC');
		$this->setSaveParametersInSession(false);
		$this->setUseAjax(true);
	}
	
	protected function _getCollectionClass(){
        return 'ts_reports/reportitem_collectionmini';
    }

    protected function _prepareCollection(){
		$collection = Mage::getResourceModel($this->_getCollectionClass());		
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
	
	protected function _prepareColumns(){
		$types = Mage::getModel('ts_reports/types');
		$types = $types::getTypeNames();
		
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
		
		$this->addColumn('price_type', array(
			'header'    => Mage::helper('ts_reports')->__('Price Type'),
			'index'     => 'price_type',
			'align'     => 'left',
			'width'     => '100px',
			'type'		=> 'options',
			'options'	=> $types,
			'renderer'	=> 'TS_Reports_Block_Renderer_Typename'
		));

		$this->addColumn('original_prices', array(
			'header'    => Mage::helper('ts_reports')->__('Original price'),
			'index'     => 'original_prices',
			'align'     => 'left',
			'width'     => '100px',
			'type'		=> 'currency',
			'currency'	=> 'base_currency_code',
			'renderer'	=> 'TS_Reports_Block_Renderer_Currencies'
		));
		
		$this->addColumn('order_date', array(
			'header'    => Mage::helper('ts_reports')->__('Order Date'),
			'index'     => 'order_date',
			'align'     => 'left',
			'width'     => '150px',
			'type'		=> 'date',
			'renderer'	=> 'TS_Reports_Block_Renderer_Dates'
		));
		return parent::_prepareColumns();
	}

	public function getGridUrl(){
		return $this->getUrl('*/*/grid', array('_current' => true));
	}
}
