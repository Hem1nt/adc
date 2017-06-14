<?php

/**
 * Out of Stock Subscriber controller
 *
 * @category   BusinessKing
 * @package    BusinessKing_OutofStockSubscription
 */
class Iksula_OutofStockSubscription_Adminhtml_SubscriberController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed(){
            // return true;
            return Mage::getSingleton('admin/session')->isAllowed('outofstocksubscription');  
        }
        
    public function IndexAction()
    {

    	$this->getLayout()->createBlock('outofstocksubscription/adminhtml_subcriber');
        $this->loadLayout();
        $this->renderLayout();
    }

    public function deleteAction()
    {
      $id  = (int) $this->getRequest()->getParam('id');

      $model = Mage::getModel('outofstocksubscription/info');
        try {

            $model->setId($id)->delete();
            $refererUrl = $this->getRequest()->getServer('HTTP_REFERER');
            $this->getResponse()->setRedirect($refererUrl);
            Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
            return $this;

        } catch (Exception $e){
            // echo $e->getMessage();
            Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
            $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
        }
    }

        public function massRemoveAction()
        {
            try {
                $ids = $this->getRequest()->getPost('ids', array());
                foreach ($ids as $id) {
                      $model = Mage::getModel("outofstocksubscription/info");
                      $model->setId($id)->delete();
                }
                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
            }
            catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
            }
            $this->_redirect('*/*/');
        }
            
        /**
         * Export order grid to CSV format
         */
        public function exportCsvAction()
        {
            $fileName   = 'outofstocksubscription.csv';
            $grid       = $this->getLayout()->createBlock('outofstocksubscription/adminhtml_subcriber_export');
            $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
        } 
        /**
         *  Export order grid to Excel XML format
         */
        public function exportExcelAction()
        {
            $fileName   = 'outofstocksubscription.xml';
            $grid       = $this->getLayout()->createBlock('outofstocksubscription/adminhtml_subcriber_export');
            $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
        }


}
