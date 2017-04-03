<?php

class Iksula_Categorysales_Block_Adminhtml_Categorysalescustomer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()    {
        parent::__construct();
        $this->setSaveParametersInSession(true);
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
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
        $email = $filterdata['email'];
        $customer = Mage::getModel("customer/customer");
        $customer->setWebsiteId(Mage::app()->getWebsite('admin')->getId());
        $item = $customer->loadByEmail($email);
        $customerAddressId = $item->getData('default_billing');
        $addressCollection = Mage::getModel('customer/address')->load($customerAddressId);
        $address = $addressCollection->getData();

        // print_r()
        $collection = new Varien_Data_Collection();
        $varienObject = new Varien_Object();
        $varienObject->setfirstname($address['firstname']);
        $varienObject->setlastname($address['lastname']);
        $varienObject->settelephone($address['telephone']);
        $varienObject->setstreet($address['street']);
        $varienObject->setcity($address['city']);
        $varienObject->setregion($address['region']);
        $varienObject->setcountry_id($address['country_id']);
        $collection->addItem($varienObject);
        return $collection;
    }


    // prepare columns and collection

    /**
     * Prepare our grid's columns to display
     * @return My_Reports_Block_Adminhtml_Grid
     */
    protected function _prepareColumns()
    {
        // add base grand total w/ a currency renderer, and add totals

        $this->addColumn('firstname', array(
            'header'            => Mage::helper('iksula_categorysales')->__('Firstname'),
            'index'             => 'firstname',
            'filter'            => false,
            'sortable'          => false
        ));

        $this->addColumn('lastname', array(
            'header'            => Mage::helper('iksula_categorysales')->__('Lastname'),
            'index'             => 'lastname',
            'filter'            => false,
            'sortable'          => false
        ));

        $this->addColumn('telephone', array(
            'header'            => Mage::helper('iksula_categorysales')->__('Mobile'),
            'index'             => 'telephone',
            'filter'            => false,
            'sortable'          => false
        ));

        $this->addColumn('street', array(
            'header'            => Mage::helper('iksula_categorysales')->__('Street'),
            'index'             => 'street',
            'filter'            => false,
            'sortable'          => false
        ));

        $this->addColumn('city', array(
            'header'            => Mage::helper('iksula_categorysales')->__('City'),
            'index'             => 'city',
            'filter'            => false,
            'sortable'          => false
        ));

        $this->addColumn('region', array(
            'header'            => Mage::helper('iksula_categorysales')->__('State'),
            'index'             => 'region',
            'filter'            => false,
            'sortable'          => false
        ));

        $this->addColumn('country_id', array(
            'header'            => Mage::helper('iksula_categorysales')->__('Country'),
            'index'             => 'country_id',
            "renderer"          =>"Iksula_Categorysales_Block_Adminhtml_Renderer_Country",
            'filter'            => false,
            'sortable'          => false
        ));


        // add export types
        // $this->addExportType('*/*/exportCsv', Mage::helper('iksula_categorysales')->__('CSV'));
        // $this->addExportType('*/*/exportExcel', Mage::helper('iksula_categorysales')->__('MS Excel XML'));

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
        $resourceCollection     = $this->getResourceCollection($filterData);

        // attach the prepared collection to our grid
        $this->setCollection($resourceCollection);
        // parent::_prepareCollection();
        // return $this;
    }

}
