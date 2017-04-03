<?php
class Iksula_Overrides_Block_Adminhtml_Sales_Order extends Mage_Adminhtml_Block_Sales_Order
{
	 public function __construct()
    {
        $this->_controller = 'sales_order';
        $this->_headerText = Mage::helper('sales')->__('Orders');
        $this->_addButtonLabel = Mage::helper('sales')->__('Create New Order');
        parent::__construct();
        if (!Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/create')) {
            $this->_removeButton('add');
        }
		//add button for change status import csv

        if (Mage::getSingleton('admin/session')->isAllowed('simpleorderexport/actions/importcsv')) {            
            $this->_addButton(
                'importcsv', array(
                'label' => Mage::helper('sales')->__('Import CSV'),
                'onclick' => "window.location.href='" . $this->getUrl('*/sales_order/importcsv') . "'",
                'level' => -2
                    )
            );
        }
         if (Mage::getSingleton('admin/session')->isAllowed('simpleorderexport/actions/importtracking')) {
            $this->_addButton(
                'importtracking', array(
                'label' => Mage::helper('sales')->__('Import Trakingnumber'),
                'onclick' => "window.location.href='" . $this->getUrl('*/sales_order/importtracking') . "'",
                'level' => -3
                )
		    );
         }
         if (Mage::getSingleton('admin/session')->isAllowed('simpleorderexport/actions/bulkinvoice')) {
            $this->_addButton(
                'bulkinvoice', array(
                'label' => Mage::helper('sales')->__('Bulk Invoice'),
                'onclick' => "window.location.href='" . $this->getUrl('*/sales_order/bulkinvoice') . "'",
                'level' => -4
                )
            ); 
         }
         if (Mage::getSingleton('admin/session')->isAllowed('simpleorderexport/actions/bulkshipment')) {
            $this->_addButton(
                    'bulkshipment', array(
                    'label' => Mage::helper('sales')->__('Bulk Shipment'),
                    'onclick' => "window.location.href='" . $this->getUrl('*/sales_order/bulkshipment') . "'",
                    'level' => -5
                )
            );
         }  
         if (Mage::getSingleton('admin/session')->isAllowed('simpleorderexport/actions/customexporttocsv')) {
            $this->_addButton(
                    'customexporttocsv', array(
                    'label' => Mage::helper('sales')->__('Bulk Export'),
                    'onclick' => "window.location.href='" . $this->getUrl('*/sales_order/customexporttocsv') . "'",
                    'level' => -6
                )
            );
         }
         if (Mage::getSingleton('admin/session')->isAllowed('simpleorderexport/actions/itemexporttocsv')) {
            $this->_addButton(
                    'itemexporttocsv', array(
                    'label' => Mage::helper('sales')->__('Bulk Item Export'),
                    'onclick' => "window.location.href='" . $this->getUrl('*/sales_order/itemexporttocsv') . "'",
                    'level' => -7
                )
            );

         }
        if (Mage::getSingleton('admin/session')->isAllowed('simpleorderexport/actions/feedbackimportcsv')) {
             $this->_addButton(
                'feedbackimportcsv', array(
                    'label' => Mage::helper('sales')->__('Review Import'),
                    'onclick' => "window.location.href='" . $this->getUrl('*/sales_order/feedbackimportcsv') . "'",
                    'level' => -7
                )
            );
        }
        if (Mage::getSingleton('admin/session')->isAllowed('simpleorderexport/actions/prescriptionexportcsv')) {
            $this->_addButton(
            'prescriptionexportcsv', array(
                'label' => Mage::helper('sales')->__('Report Export'),
                'onclick' => "window.location.href='" . $this->getUrl('*/sales_order/prescriptionexportcsv') . "'",
                'level' => -7
                )
            );
        }
        if (Mage::getSingleton('admin/session')->isAllowed('simpleorderexport/actions/descripterexportcsv')) {
            $this->_addButton(
            'descripterexportcsv', array(
                'label' => Mage::helper('sales')->__('Descripter Import Export'),
                'onclick' => "window.location.href='" . $this->getUrl('*/sales_order/descripterexportcsv') . "'",
                'level' => -8
                )
            );
        }

        if (Mage::getSingleton('admin/session')->isAllowed('simpleorderexport/actions/invoice_pdf')) {
            $this->_addButton(
            'invoice_pdf', array(
                'label' => Mage::helper('sales')->__('Invoice Pdf'),
                'onclick' => "window.location.href='" . $this->getUrl('*/sales_order/bulkinvoicepdf') . "'",
                'level' => -9
                )
            );
        }
    }
}
			