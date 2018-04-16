<?php
class EM_DeleteOrder_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
     public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _getCollectionClass()
    {
        return 'sales/order_grid_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());

        $this->setCollection($collection);
        parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
            'filter_index' => 'increment_id',
        ));

        $this->addColumn('customer_order_increment_id', array(
            'header'=> Mage::helper('sales')->__('Custom Order #'),
            'width' => '80px',
            'type'  => 'text',
            'filter_index' => 'customer_order_increment_id',
            'index' => 'customer_order_increment_id',
        ));

        $this->addColumn('orderflag',array(
            'header'    => Mage::helper('sales')->__('Flag'),
            'width'     => '10px',
            'type'      => 'action',
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'renderer' => 'EM_DeleteOrder_Block_Adminhtml_Sales_Order_Renderer_Orderflag',
        ));

        $this->addColumn('echeck_transactionid', array(
            'header' => Mage::helper('sales')->__('Echeck Transactionid'),
            'index' => 'echeck_transactionid',
            'filter_index' => 'echeck_transactionid',
        ));
        $this->addColumn('voucher_transaction_id', array(
            'header' => Mage::helper('sales')->__('Voucher Transactionid'),
            'index' => 'voucher_transaction_id',
            'filter_index' => 'voucher_transaction_id',
        ));
        if (!Mage::app()->isSingleStoreMode()) {
        $this->addColumn('store_id', array(
            'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
            'index'     => 'store_id',
            'type'      => 'store',
            'store_view'=> true,
            'display_deleted' => true,
        ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
            'filter_index' => 'created_at',
        ));

        // $this->addColumn('customer_telephone', array(
        //     'header' => Mage::helper('sales')->__('Telephone'),
        //     'index' => 'customer_telephone',
        //     'type' => 'text',
        //     'filter_index' => 'customer_telephone',
        // ));
        
        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
            'filter_index' => 'billing_name',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));

        $this->addColumn('lastcomment',array(
            'header'    => Mage::helper('sales')->__('Last Comment'),
            'width'     => '50px',
            'type'      => 'action',
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'renderer' => 'EM_DeleteOrder_Block_Adminhtml_Sales_Order_Renderer_Lastcomment',
        ));

        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
            'filter_index' => 'base_grand_total',
        ));

        $this->addColumn('country_id', array(
            'header' => Mage::helper('sales')->__('Country'),
            'index' => 'country_id',
            'type'=> 'options',
            'options'=>$this->getAllCountry(),
            'filter_index' => 'country_id',
        ));

        // $this->addColumn('grand_total', array(
        //     'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
        //     'index' => 'grand_total',
        //     'type'  => 'currency',
        //     'currency' => 'order_currency_code',
        //     'filter_index' => 'grand_total',
        // ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
            'filter_index' => 'status',
        ));

        // if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
        //     $this->addColumn('action',array(
        //         'header'    => Mage::helper('sales')->__('Action'),
        //         'width'     => '50px',
        //         'type'      => 'action',
        //         'getter'     => 'getId',
        //         'actions'   => array(
        //             array(
        //                 'caption' => Mage::helper('sales')->__('View'),
        //                 'url'     => array('base'=>'*/sales_order/view'),
        //                 'field'   => 'order_id'
        //             )
        //         ),
        //         'filter'    => false,
        //         'sortable'  => false,
        //         'index'     => 'stores',
        //         'is_system' => true,
        //     ));
        // }
        
        
        /* customer Group Filter */
        
        $groups = Mage::getResourceModel('customer/group_collection')
        ->addFieldToFilter('customer_group_id', array('gteq' => 0))
        ->load()
        ->toOptionHash();
        
        //$groups[0] = "Guest";

        $this->addColumn('customer_group_id', array(
            'header' => Mage::helper('sales')->__('Customer Group'),
            'index'  => 'customer_group_id',
            'type'   =>  'options',
            'width'  => '80px',
            'filter_index' => 'customer_group_id',
            'options'      =>  $groups,
        ));

        $this->addColumn('major_comment', array(
            'header'=> Mage::helper('sales')->__('Major Comment'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'major_comment',
            'filter_index' => 'major_comment',
        ));

        /*customer group end*/

        //  $this->addColumn('browser_details', array(
        //     'header' => Mage::helper('sales')->__('Browser Details'),
        //     'index' => 'browser_details',
        //     'type' => 'text',
        //     'filter_index' => 'browser_details',
        // ));

        if (Mage::getSingleton('admin/session')->isAllowed('simpleorderexport/actions/exportCsv')) {
           $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        }

        return parent::_prepareColumns();
    }


    public function getAllCountry(){
        $options = Mage::getResourceModel('directory/country_collection')->load()->toOptionArray();
        $countries = array();
        foreach($options as $options){
            $countries[$options['value']]=$options['label'];
        }
        return $countries;
    }

    public function getOrdersCsvFile()
    {
        $this->_isExport = true;
        $this->_prepareGrid();
        $this->removeColumn('telephone');
        $this->removeColumn('shipping_name');
        $this->removeColumn('lastcomment');

        $io = new Varien_Io_File();
    

        $path = Mage::getBaseDir('var') . DS . 'export' . DS;
        $name = md5(microtime());
        $file = $path . DS . $name . '.csv';

        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($file, 'w+');
        $io->streamLock(true);
        $io->streamWriteCsv($this->_getExportHeaders());

        $this->_exportIterateCollection('_exportCsvItem', array($io));

        if ($this->getCountTotals()) {
            $io->streamWriteCsv($this->_getExportTotals());
        }

        $io->streamUnlock();
        $io->streamClose();

        return array(
            'type'  => 'filename',
            'value' => $file,
            'rm'    => true // can delete file after use
        );
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        $this->getMassactionBlock()->setUseSelectAll(true);

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/cancel')) {
            $this->getMassactionBlock()->addItem('cancel_order', array(
                 'label'=> Mage::helper('sales')->__('Cancel'),
                 'url'  => $this->getUrl('*/sales_order/massCancel'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/hold')) {
            $this->getMassactionBlock()->addItem('hold_order', array(
                 'label'=> Mage::helper('sales')->__('Hold'),
                 'url'  => $this->getUrl('*/sales_order/massHold'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/unhold')) {
            $this->getMassactionBlock()->addItem('unhold_order', array(
                 'label'=> Mage::helper('sales')->__('Unhold'),
                 'url'  => $this->getUrl('*/sales_order/massUnhold'),
            ));
        }
       
         if (Mage::getSingleton('admin/session')->isAllowed('simpleorderexport/actions/pdfinvoices')) {
            $this->getMassactionBlock()->addItem('pdfinvoices_order', array(
               'label'=> Mage::helper('sales')->__('Print Invoices'),
               'url'  => $this->getUrl('*/sales_order/pdfinvoices'),
               ));
        }   

        if (Mage::getSingleton('admin/session')->isAllowed('simpleorderexport/actions/pdfshipments')) {
            $this->getMassactionBlock()->addItem('pdfshipments_order', array(
               'label'=> Mage::helper('sales')->__('Print Packingslips'),
               'url'  => $this->getUrl('*/sales_order/pdfshipments'),
               ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('simpleorderexport/actions/pdfcreditmemos')) {
            $this->getMassactionBlock()->addItem('pdfcreditmemos_order', array(
               'label'=> Mage::helper('sales')->__('Print Credit Memos'),
               'url'  => $this->getUrl('*/sales_order/pdfcreditmemos'),
               ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('simpleorderexport/actions/pdfdocs')) {
            $this->getMassactionBlock()->addItem('pdfdocs_order', array(
               'label'=> Mage::helper('sales')->__('Print All'),
               'url'  => $this->getUrl('*/sales_order/pdfdocs'),
          ));
        } 

        if (Mage::getSingleton('admin/session')->isAllowed('simpleorderexport/actions/deleteorder')) {
            $this->getMassactionBlock()->addItem('delete_order', array(
               'label'=> Mage::helper('sales')->__('Delete order'),
               'url'  => $this->getUrl('*/sales_order/deleteorder'),
               ));
        }
                  
        if (Mage::getSingleton('admin/session')->isAllowed('simpleorderexport/actions/csvexport')) {
            $this->getMassactionBlock()->addItem('simpleorderexport',array(
                'label'=>$this->__('Export to .csv file'),
                'url'=>$this->getUrl('simpleorderexport/export_order/csvexport'))
            );
        }

        if (Mage::getSingleton('admin/session')->isAllowed('simpleorderexport/actions/couponreportexport')) {
            $this->getMassactionBlock()->addItem('couponreportexport',array(
                'label'=>$this->__('Coupon Code Report'),
                'url'=>$this->getUrl('simpleorderexport/export_order/couponreportexport'))
            );
        }

        if (Mage::getSingleton('admin/session')->isAllowed('simpleorderexport/actions/descriptorreportexport')) {
            $this->getMassactionBlock()->addItem('descriptorreportexport',array(
                'label'=>$this->__('Descriptor Wise Report'),
                'url'=>$this->getUrl('simpleorderexport/export_order/descriptorreportexport'))
            );
        }   
        
        return $this;
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

}