<?php

class Iksula_Categorysales_Block_Adminhtml_Categorysalesview_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
     public function __construct()
    {
        parent::__construct();
        $this->setId('categorysalesviewGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
       }

    //  public function getModelCollection()
    // {
    //     return $resourceCollection = Mage::getModel('sales/order_item')->getCollection();
    // }

    /**
     * Factory method for our resource collection
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    public function getResourceCollection($filterdata)
    {
        $email = $filterdata['email'];
        // echo "<pre>"; print_r($filterdata['email']);  exit;

        $itemsCollection = Mage::getModel('sales/order')->getCollection()
                        ->addAttributeToSelect('increment_id')
                        ->addAttributeToSelect('customer_email')
                        ->addAttributeToSelect('grand_total')
                        ->addAttributeToSelect('status')
                        ->addAttributeToSelect('created_at')
                        ->addAttributeToSelect('browser_details')
                        ->addAttributeToFilter('customer_email', $email);
        $itemsCollection->getSelect()->join(
                array(
                    'sales_order_item'=>Mage::getSingleton('core/resource')->getTableName('sales/order_item')),
                    'main_table.entity_id = sales_order_item.order_id',
                    array('sales_order_item.name')
                    );

        $itemsCollection->getSelect()->join(
                array(
                    'address'=>Mage::getSingleton('core/resource')->getTableName('sales/order_address')),
                    'main_table.entity_id = address.parent_id',
                    array('address.country_id','address.telephone','address.region','address.city','address.street','address.region_id')
                );


        return $itemsCollection;
    }


    // prepare columns and collection

    /**
     * Prepare our grid's columns to display
     * @return My_Reports_Block_Adminhtml_Grid
     */
    protected function _prepareColumns()
    {

        // add base grand total w/ a currency renderer, and add totals
        $this->addColumn('increment_id', array(
            'header'            => Mage::helper('iksula_categorysales')->__('Order ID'),
            'index'             => 'increment_id',
        ));

        $this->addColumn('grand_total', array(
            'header'            => Mage::helper('iksula_categorysales')->__('Amount'),
            'index'             => 'grand_total',
        ));

        $this->addColumn('created_at', array(
            'header'            => Mage::helper('iksula_categorysales')->__('Purchased On'),
            'index'             => 'created_at',
        ));

        $this->addColumn('name', array(
            'header'            => Mage::helper('iksula_categorysales')->__('Products'),
            'index'             => 'name',
        ));

        $this->addColumn('country_id', array(
            'header'            => Mage::helper('iksula_categorysales')->__('Country'),
            'index'             => 'country_id',
             "renderer"          =>"Iksula_Categorysales_Block_Adminhtml_Renderer_Country",
        ));

        $this->addColumn('browser_details', array(
            'header'            => Mage::helper('iksula_categorysales')->__('Browser Details'),
            'index'             => 'browser_details',
        ));


        // add export types
        $this->addExportType('*/*/exportCsv', Mage::helper('iksula_categorysales')->__('CSV'));
        // $this->addExportType('*/*/exportNewExcel', Mage::helper('iksula_categorysales')->__('MS Excel XML'));

        return parent::_prepareColumns();
    }

   /**
     * Prepare our collection which we'll display in the grid
     * First, get the resource collection we're dealing with, with our custom filters applied.
     * In case of an export, we're done, otherwise calculate the totals
     * @return My_Reports_Block_Adminhtml_Grid
     */
    protected function _prepareCollection()
    {
        $filterData = $this->getFilterData();
        $resourceCollection = $this->getResourceCollection($filterData);

        // attach the prepared collection to our grid
        $this->setCollection($resourceCollection);
        return parent::_prepareCollection();
        // return $this;
    }

}
