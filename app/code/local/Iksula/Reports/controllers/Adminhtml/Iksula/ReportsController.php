<?php

class Iksula_Reports_Adminhtml_Iksula_ReportsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initialize titles and navigation breadcrumbs
     * @return My_Reports_Adminhtml_ReportsController
     */

    protected function _isAllowed(){
        // return true;
        return Mage::getSingleton('admin/session')->isAllowed('report/salesroot');  
    }

    protected function _initAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Custom Reports'));
        $this->loadLayout()
            ->_setActiveMenu('report/sales')
            ->_addBreadcrumb(Mage::helper('iksula_reports')->__('Reports'), Mage::helper('iksula_reports')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('iksula_reports')->__('Sales'), Mage::helper('iksula_reports')->__('Sales'))
            ->_addBreadcrumb(Mage::helper('iksula_reports')->__('Custom Reports'), Mage::helper('iksula_reports')->__('Custom Reports'));
        return $this;
    }

    /**
     * Prepare blocks with request data from our filter form
     * @return My_Reports_Adminhtml_ReportsController
     */
    protected function _initReportAction($blocks)
    {
        if (!is_array($blocks)) {
            $blocks = array($blocks);
        }

        $requestData    = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
        $requestData    = $this->_filterDates($requestData, array('from', 'to'));
        $params         = $this->_getDefaultFilterData();
        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }

        foreach ($blocks as $block) {
            if ($block) {
                $block->setFilterData($params);
            }
        }
        return $this;
    }


    protected function _custominitReportAction($blocks,$params)
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

        $gridBlock = $this->getLayout()->getBlock('adminhtml_report.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');
        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    public function viewAction() {
        $params['from_date'] = $this->getRequest()->getParam('from_date');
        $params['to_date'] = $this->getRequest()->getParam('to_date');
        $params['usertype'] = $this->getRequest()->getParam('usertype');

        $this->_initAction();
        $gridBlock = $this->getLayout()->getBlock('adminhtml_reportview.grid');
        $this->_custominitReportAction(array(
            $gridBlock,
        ),$params);
        $this->renderLayout();
    }

    /**
     * Export reports to CSV file
     */
    public function exportCsvAction()
    {
        $fileName   = 'iksula_reports.csv';
        $grid       = $this->getLayout()->createBlock('iksula_reports/adminhtml_report_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * Export reports to Excel XML file
     */
    public function exportExcelAction()
    {
        $fileName   = 'iksula_reports.xml';
        $grid       = $this->getLayout()->createBlock('iksula_reports/adminhtml_report_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile());
    }

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
}
