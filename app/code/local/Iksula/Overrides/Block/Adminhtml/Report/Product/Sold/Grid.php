<?php
class Iksula_Overrides_Block_Adminhtml_Report_Product_Sold_Grid extends Mage_Adminhtml_Block_Report_Product_Sold_Grid
{
	protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    =>Mage::helper('reports')->__('Product Name'),
            'index'     =>'order_items_name',
            'width'     =>'200px',
        ));
        
        $this->addColumn('ordered_sku', array(
            'header'    =>Mage::helper('reports')->__('Sku'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'sku',
            'type'      =>'number'
        ));

        $this->addColumn('ordered_qty', array(
            'header'    =>Mage::helper('reports')->__('Quantity Ordered'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'ordered_qty',
            'total'     =>'sum',
            'type'      =>'number'
        ));

        $this->addColumn('ordered_ids', array(
            'header'    =>Mage::helper('reports')->__('Category Ids'),
            'width'     =>'120px',
            'align'     =>'right',
            'index'     =>'ordered_ids',
            'renderer'  => 'Iksula_Overrides_Block_Adminhtml_Report_Product_Renderer_CategoryIds',// THIS IS WHAT THIS POST IS ALL ABOUT
        ));

        $this->addColumn('ordered_category', array(
            'header'    =>Mage::helper('reports')->__('Category Name'),
            'width'     =>'240px',
            'align'     =>'right',
            'index'     =>'ordered_category',
            'renderer'  => 'Iksula_Overrides_Block_Adminhtml_Report_Product_Renderer_Category',// THIS IS WHAT THIS POST IS ALL ABOUT
        ));

        $this->addExportType('*/*/exportSoldCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportSoldExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
			