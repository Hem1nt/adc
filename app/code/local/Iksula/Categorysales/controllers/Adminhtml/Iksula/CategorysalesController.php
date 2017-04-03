<?php

class Iksula_Categorysales_Adminhtml_Iksula_CategorysalesController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initialize titles and navigation breadcrumbs
     * @return My_Reports_Adminhtml_ReportsController
     */
    protected function _initAction()
    {
        $this->_title($this->__('CategorySales'))->_title($this->__('Sales'))->_title($this->__('Custom Category Sales Reports'));
        $this->loadLayout()
            ->_setActiveMenu('report/sales')
            ->_addBreadcrumb(Mage::helper('iksula_categorysales')->__('Reports'), Mage::helper('iksula_categorysales')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('iksula_categorysales')->__('Sales'), Mage::helper('iksula_categorysales')->__('Sales'))
            ->_addBreadcrumb(Mage::helper('iksula_categorysales')->__('Custom Category Sales Reports'), Mage::helper('iksula_categorysales')->__('Custom Category Sales Reports'));
        return $this;
    }

    /**
     * Prepare blocks with request data from our filter form
     * @return My_Reports_Adminhtml_ReportsController
     */
    protected function _initReportAction($blocks,$params)
    {
        foreach ($blocks as $block) {
            if ($block) {
                $block->setFilterData($params);
            }
        }

        return $this;
    }

    /**
     * Grid action
     */
    public function indexAction()
    {
        $this->_initAction();

        $gridBlock = $this->getLayout()->getBlock('adminhtml_categorysales.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $param = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));

        $params['from_date'] = $param['from'];
        $params['to_date'] = $param['to'];
        $params['category_id'] = $param['category_id'];

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ),$params);

        $this->renderLayout();
    }

    public function viewAction() {
        $params['email'] = $this->getRequest()->getParam('email');
        $this->_initAction();
        $gridBlock = $this->getLayout()->getBlock('adminhtml_categorysalesview.grid');
        $customergridBlock = $this->getLayout()->getBlock('adminhtml_categorysalescustomer.grid');
        $this->_initReportAction(array(
            $customergridBlock,
            $gridBlock
        ),$params);
        $this->renderLayout();
    }

    /**
     * Export reports to CSV file
     */
    public function exportCsvAction()
    {
        $fileName   = 'iksula_categorysales.csv';
        // $content    = $this->getLayout()->createBlock('iksula_categorysales/adminhtml_categorysalesview_grid')
        //     ->getCsv();

        // $this->_sendUploadResponse($fileName, $content);

        $grid = $this->getLayout()->createBlock('iksula_categorysales/adminhtml_categorysalesview_grid');
        $csvfile = $grid->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    // public function exportNewCsvAction()
    // {
    //     $fileName   = 'iksula_categorysalesview.csv';
    //     $grid    = $this->getLayout()->createBlock('iksula_categorysales_block_adminhtml_categorysalesview_grid');
    //     $grid       = $this->getLayout()->createBlock('iksula_categorysales/adminhtml_categorysalesview_grid');
    //     // $this->_initReportAction($grid,$param);
    //     $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    // }

    /**
     * Export reports to Excel XML file
     */
    // public function exportExcelAction()
    // {
    //     $fileName   = 'iksula_categorysales.xml';
    //     // $grid    = $this->getLayout()->createBlock('iksula_categorysales_block_adminhtml_categorysales_grid');
    //     $grid       = $this->getLayout()->createBlock('iksula_categorysales/adminhtml_categorysales_grid');
    //     // $this->_initReportAction($grid,$param);
    //     $this->_prepareDownloadResponse($fileName, $grid->getExcelFile());
    // }

    // public function exportNewExcelAction()
    // {
    //     $fileName   = 'iksula_categorysalesview.xml';
    //     $grid    = $this->getLayout()->createBlock('iksula_categorysales_block_adminhtml_categorysalesview_grid');
    //     // $grid       = $this->getLayout()->createBlock('iksula_categorysales/adminhtml_categorysalesview_grid');
    //     // $this->_initReportAction($grid,$param);
    //     $this->_prepareDownloadResponse($fileName, $grid->getExcelFile());
    // }

    /**
     * Returns default filter data
     * @return Varien_Object
     */
    protected function _getDefaultFilterData()
    {
        return new Varien_Object(array(
            'from'      => date('Y-m-d G:i:s', strtotime('-1 month -1 day')),
            'to'        => date('Y-m-d G:i:s', strtotime('-1 day'))
        ));
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}
