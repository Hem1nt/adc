<?php

class Iksula_Categorysales_Block_Adminhtml_Categorysales_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('categorysalesGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

     public function getModelCollection()
    {
        return $resourceCollection = Mage::getModel('sales/order_item')->getCollection();
    }

    /**
     * Factory method for our resource collection
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    public function getResourceCollection($filterdata)
    {
        $fromdate = date('Y-m-d H:i:s',strtotime($filterdata['from_date']));
        $todate = date('Y-m-d H:i:s',strtotime($filterdata['to_date']));
        $category_id = $filterdata['category_id'];
        // $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        // $result = $read->query("
        //     SELECT CONCAT(address.firstname,' ', address.lastname) AS Name, address.email AS Email,address.telephone AS Phone,items.created_at AS Date,items.name AS ProductName, items.order_id AS Orderref, items.category_id as category_id FROM sales_flat_order AS orders JOIN sales_flat_order_item AS items ON items.order_id = orders.entity_id LEFT JOIN sales_flat_order_address AS address ON orders.entity_id = address.parent_id INNER JOIN catalog_category_product as category_ids ON items.product_id =category_ids.product_id WHERE FIND_IN_SET('$category_id',items.category_id) AND items.created_at BETWEEN '$fromdate' AND '$todate'
        //     ");
        // $row = $result->fetchAll();

        $items = Mage::getModel('sales/order_item')->getCollection()
                        ->addAttributeToSelect('created_at')
                        ->addAttributeToSelect('name');
        $items->getSelect()->join(
                array(
                    'sales_order'=>Mage::getSingleton('core/resource')->getTableName('sales/order')),
                    'main_table.order_id = sales_order.entity_id',
                    array('sales_order.customer_firstname','sales_order.customer_lastname','sales_order.customer_email')
                    );
        $items->getSelect()->join(
        array(
            'category_product'=>Mage::getSingleton('core/resource')->getTableName('catalog/category_product')),
            'main_table.product_id = category_product.product_id',
             array('category_product.category_id')
        );

        $items->getSelect()->join(
                array(
                    'address'=>Mage::getSingleton('core/resource')->getTableName('sales/order_address')),
                    'sales_order.entity_id = address.parent_id',
                    array('address.telephone')
                )->where("FIND_IN_SET('$category_id',category_product.category_id) AND main_table.created_at BETWEEN '$fromdate' AND '$todate'");

        return $items;
    }


    // prepare columns and collection

    /**
     * Prepare our grid's columns to display
     * @return My_Reports_Block_Adminhtml_Grid
     */
    protected function _prepareColumns()
    {
        // add base grand total w/ a currency renderer, and add totals

        $this->addColumn('customer_email', array(
            'header'            => Mage::helper('iksula_categorysales')->__('Email'),
            'index'             => 'customer_email',
            "renderer"          =>"Iksula_Categorysales_Block_Adminhtml_Renderer_Customer",
        ));

        $this->addColumn('customer_firstname', array(
            'header'            => Mage::helper('iksula_categorysales')->__('Customer FirstName'),
            'index'             => 'customer_firstname',
        ));

        $this->addColumn('customer_lastname', array(
            'header'            => Mage::helper('iksula_categorysales')->__('Customer LastName'),
            'index'             => 'customer_lastname',
        ));

        $this->addColumn('telephone', array(
            'header'            => Mage::helper('iksula_categorysales')->__('Phone No'),
            'index'             => 'telephone',
        ));

        $this->addColumn('name', array(
            'header'            => Mage::helper('iksula_categorysales')->__('Product Name'),
            'index'             => 'name',
        ));

        $this->addColumn('created_at', array(
            'header'            => Mage::helper('iksula_categorysales')->__('Purchased On'),
            'index'             => 'created_at',
        ));


        // add export types
        $this->addExportType('*/*/exportCsv', Mage::helper('iksula_categorysales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('iksula_categorysales')->__('MS Excel XML'));

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
        $filterData             = $this->getFilterData();
        $resourceCollection     = $this->getResourceCollection($filterData);
        $this->_prepareColumns($filterData);

        // attach the prepared collection to our grid
        $this->setCollection($resourceCollection);
        parent::_prepareCollection();
        return $this;
    }

}
