<?php

class Iksula_Reports_Block_Adminhtml_Reportview_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('reportsGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    public function getModelCollection()
    {
        return $resourceCollection = Mage::getModel('sales/order')->getCollection();
    }

    public function getCustomerCollection()
    {
        return $resourceCollection = Mage::getModel('customer/customer')->getCollection();
    }


    /**
     * Factory method for our resource collection
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    public function getResourceCollection($filterdata)
    {
        $fromdate = $filterdata['from_date'];
        $todate = $filterdata['to_date'];
        $usertype = $filterdata['usertype'];
        // echo "<pre>";

        $resourceCollection = $this->getModelCollection();
        $resourceCollection1 = $this->getModelCollection();
        $resourceCollection2 = $this->getModelCollection();

        if($usertype == 'guest') {
            $customerCollection = $resourceCollection
                ->distinct(true)
                ->addFieldToFilter('customer_is_guest', 1)
                ->addFieldToFilter('customer_group_id', 0)
                ->addFieldToFilter('created_at', array('from' => $fromdate, 'to' => $todate))
                ->addAttributeToSelect('customer_email')
                ->addAttributeToSelect('customer_id')
                ->addAttributeToSelect('customer_lastname')
                ->addAttributeToSelect('customer_gender')
                ->addAttributeToSelect('customer_group_id')
                ->addAttributeToSelect('customer_dob')
                ->addAttributeToSelect('customer_firstname');
        }

        $collection = new Varien_Data_Collection();

        if($usertype == 'old') {
            $customerCollection = $resourceCollection1
                ->distinct(true)
                ->addFieldToFilter('customer_group_id', 6)
                ->addFieldToFilter('created_at', array('from' => $fromdate, 'to' => $todate))
                ->addAttributeToSelect('customer_email')
                ->addAttributeToSelect('customer_id')
                ->addAttributeToSelect('customer_lastname')
                ->addAttributeToSelect('customer_gender')
                ->addAttributeToSelect('customer_group_id')
                ->addAttributeToSelect('customer_dob')
                ->addAttributeToSelect('customer_firstname');
        }

        if($usertype == 'new') {
            $customerCollection = $resourceCollection2
                ->distinct(true)
                ->addFieldToFilter('customer_group_id', 1)
                ->addFieldToFilter('created_at', array('from' => $fromdate, 'to' => $todate))

                ->addAttributeToSelect('customer_email')
                ->addAttributeToSelect('customer_id')
                ->addAttributeToSelect('customer_lastname')
                ->addAttributeToSelect('customer_gender')
                ->addAttributeToSelect('customer_group_id')
                ->addAttributeToSelect('customer_dob')
                ->addAttributeToSelect('customer_firstname');
        }

        return $customerCollection;
    }

    // prepare columns and collection

    /**
     * Prepare our grid's columns to display
     * @return My_Reports_Block_Adminhtml_Grid
     */
    protected function _prepareColumns()
    {
        // add base grand total w/ a currency renderer, and add totals
        $this->addColumn('customer_id', array(
            'header'            => Mage::helper('iksula_reports')->__('Customer Id'),
            'index'             => 'customer_id',

            // 'total'             => 'sum'
        ));

        $this->addColumn('customer_email', array(
            'header'            => Mage::helper('iksula_reports')->__('Customer Email'),
            'index'             => 'customer_email',

            // 'total'             => 'sum'
        ));

        $this->addColumn('customer_firstname', array(
            'header'            => Mage::helper('iksula_reports')->__('Customer Firstname'),
            'index'             => 'customer_firstname',
            // 'total'             => 'sum'
        ));

        $this->addColumn('customer_lastname', array(
            'header'            => Mage::helper('iksula_reports')->__('Customer Lastname'),
            'index'             => 'customer_lastname',
            // 'total'             => 'sum'
        ));

        $this->addColumn('customer_gender', array(
            'header'            => Mage::helper('iksula_reports')->__('Customer Gender'),
            'index'             => 'customer_gender',
            'type'              => 'options',
            'options'           => $this->_getAttributeOptions('customer_gender'),
            // "renderer" =>"Iksula_Reports_Block_Adminhtml_Renderer_Gender",
            // 'total'             => 'sum'
        ));


        $this->addColumn('customer_group_id', array(
            'header'            => Mage::helper('iksula_reports')->__('Customer Group'),
            'index'             => 'customer_group_id',
            // 'type'              => 'options',
            // 'options'           => $this->_getGroupOptions('customer_group_id'),
            "renderer" =>"Iksula_Reports_Block_Adminhtml_Renderer_Group",
            // 'total'             => 'sum'
        ));

        $this->addColumn('customer_dob', array(
            'header'            => Mage::helper('iksula_reports')->__('Customer DOB'),
            'index'             => 'customer_dob',
            // 'total'             => 'sum'
        ));

        // $this->addColumn('orders_count', array(
        //     'header'    => Mage::helper('customer')->__('Orders Count'),
        //     'width'     => '50px',
        //     'index'     => 'customer_email',
        //     'type'      => 'number',
        //     "renderer" =>"Iksula_Reports_Block_Adminhtml_Renderer_Count",

        // ));

        // add export types
        $this->addExportType('*/*/exportCsv', Mage::helper('iksula_reports')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('iksula_reports')->__('MS Excel XML'));

        return parent::_prepareColumns();
    }


    protected function _getAttributeOptions($attribute_code)
    {
        $attribute = Mage::getModel('customer/customer')->getAttribute('gender', $attribute_code);
        $options = array();
        foreach( $attribute->getSource()->getAllOptions(true, true) as $option ) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }


    protected function _getGroupOptions($attribute_code)
    {
        $groupname = Mage::getModel('customer/group')->load($attribute_code)->getCustomerGroupCode();
        return $groupname;

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

        // attach the prepared collection to our grid
        $this->setCollection($resourceCollection);
        parent::_prepareCollection();
        return $this;
    }

}
