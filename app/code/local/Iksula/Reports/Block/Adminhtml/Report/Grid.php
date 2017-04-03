<?php

class Iksula_Reports_Block_Adminhtml_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

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
        $fromdate = $filterdata->getData('from');
        $todate = $filterdata->getData('to');

        $resourceCollection = $this->getModelCollection();
        $resourceCollection1 = $this->getModelCollection();
        $resourceCollection2 = $this->getModelCollection();

        $guestUserdata = $resourceCollection
                        ->addFieldToFilter('customer_is_guest', 1)
                        ->addFieldToFilter('customer_group_id', 0)
                        ->addFieldToFilter('created_at', array('from' => $fromdate, 'to' => $todate));
        $guestUser = count($guestUserdata->getdata());

        // customer group 0 and customer is guest 1 - guest
        // old+regular (customer group 6) = old
        // else - new

        $collection = new Varien_Data_Collection();
        $old_customer = $resourceCollection1
                    ->addFieldToFilter('customer_group_id', 6)
                    ->addFieldToFilter('created_at', array('from' => $fromdate, 'to' => $todate))
                    ->addAttributeToSelect('customer_id');

        $new_customer = $resourceCollection2
                    ->addFieldToFilter('customer_group_id', 1)
                    ->addFieldToFilter('created_at', array('from' => $fromdate, 'to' => $todate))
                    ->addAttributeToSelect('customer_id');


        // $userdata = $resourceCollection1
        //             // ->addFieldToFilter('customer_is_guest', 0)
        //             ->addFieldToFilter('created_at', array('from' => $fromdate, 'to' => $todate))
        //             ->addAttributeToSelect('customer_id');
        //             echo "<pre>"; print_r($userdata->getData()); exit;
        // $customerCollection = $this->getCustomerCollection();

        // $customersdata = $customerCollection
        //                 ->addAttributeToSelect('entity_id')
        //                 ->addFieldToFilter('created_at', array('from' => $fromdate, 'to' => $todate));

        // // echo "<pre>";  print_r($customersdata->getData());  exit;
        // foreach ($customersdata as $customerData) {
        //     $customerID[] = $customerData->getData('entity_id');
        // }

        // $it =  new RecursiveIteratorIterator(new RecursiveArrayIterator($userdata->getData()));
        // $optimizedUserData = iterator_to_array($it, false);

        // foreach ($optimizedUserData as $Customer) {
        //     if(in_array($Customer,$customerID)) {
        //         $new_customer[] = $Customer;
        //     } else {
        //         $old_customer[] = $Customer;
        //     }
        // }

        $newUser = count($new_customer->getData());
        $oldUser = count($old_customer->getData());
            $varienObject = new Varien_Object();
            $varienObject->setfromDate($fromdate);
            $varienObject->settoDate($todate);
            $varienObject->setGuest($guestUser);
            $varienObject->setOld($oldUser);
            $varienObject->setNew($newUser);
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
        $this->addColumn('guest', array(
            'header'            => Mage::helper('iksula_reports')->__('Guest Customer'),
            'index'             => 'guest',
            "renderer" =>"Iksula_Reports_Block_Adminhtml_Renderer_Guest",
            // 'total'             => 'sum'
        ));

        $this->addColumn('old', array(
            'header'            => Mage::helper('iksula_reports')->__('Old Customer'),
            'index'             => 'old',
            "renderer" =>"Iksula_Reports_Block_Adminhtml_Renderer_Old",
            // 'total'             => 'sum'
        ));

        $this->addColumn('new', array(
            'header'            => Mage::helper('iksula_reports')->__('New Customer'),
            'index'             => 'new',
            "renderer" =>"Iksula_Reports_Block_Adminhtml_Renderer_New",
            // 'total'             => 'sum'
        ));


        // add export types
        $this->addExportType('*/*/exportCsv', Mage::helper('iksula_reports')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('iksula_reports')->__('MS Excel XML'));

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

        // attach the prepared collection to our grid
        $this->setCollection($resourceCollection);
    }

}
