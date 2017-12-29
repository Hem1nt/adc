<?php

class Iksula_ExtendedReview_Adminhtml_ExtendedreviewController extends Mage_Adminhtml_Controller_Action
{
		protected function _isAllowed()
		{
		//return Mage::getSingleton('admin/session')->isAllowed('extendedreview/extendedreview');
			return true;
		}

		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("extendedreview/extendedreview")->_addBreadcrumb(Mage::helper("adminhtml")->__("Extendedreview  Manager"),Mage::helper("adminhtml")->__("Extendedreview Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("ExtendedReview"));
			    $this->_title($this->__("Manager Extendedreview"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("ExtendedReview"));
				$this->_title($this->__("Extendedreview"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("extendedreview/extendedreview")->load($id);
				if ($model->getId()) {
					Mage::register("extendedreview_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("extendedreview/extendedreview");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Extendedreview Manager"), Mage::helper("adminhtml")->__("Extendedreview Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Extendedreview Description"), Mage::helper("adminhtml")->__("Extendedreview Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("extendedreview/adminhtml_extendedreview_edit"))->_addLeft($this->getLayout()->createBlock("extendedreview/adminhtml_extendedreview_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("extendedreview")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		/* public function newAction()
		{

		$this->_title($this->__("ExtendedReview"));
		$this->_title($this->__("Extendedreview"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("extendedreview/extendedreview")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("extendedreview_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("extendedreview/extendedreview");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Extendedreview Manager"), Mage::helper("adminhtml")->__("Extendedreview Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Extendedreview Description"), Mage::helper("adminhtml")->__("Extendedreview Description"));


		$this->_addContent($this->getLayout()->createBlock("extendedreview/adminhtml_extendedreview_edit"))->_addLeft($this->getLayout()->createBlock("extendedreview/adminhtml_extendedreview_edit_tabs"));

		$this->renderLayout();

		} */
		public function saveAction()
		{
			$post_data=$this->getRequest()->getPost();
				if ($post_data) {

					try {			

						$model = Mage::getModel("extendedreview/extendedreview")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Extendedreview was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setExtendedreviewData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setExtendedreviewData($this->getRequest()->getPost());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
					}

				}
				$this->_redirect("*/*/");
		}



		public function deleteAction()
		{
				if( $this->getRequest()->getParam("id") > 0 ) {
					try {
						$model = Mage::getModel("extendedreview/extendedreview");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
						$this->_redirect("*/*/");
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					}
				}
				$this->_redirect("*/*/");
		}

		
		public function massRemoveAction()
		{
			try {
				$ids = $this->getRequest()->getPost('ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("extendedreview/extendedreview");
					  $model->setId($id)->delete();
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}


		public function changeStatusAction(){
			try {
				$ids = $this->getRequest()->getPost('ids', array());				
				$status = $this->getRequest()->getPost('status');
				$adminuUserEmail = Mage::helper('extendedreview')->getAdminUserEmail();
				
				foreach ($ids as $id) {                      
					  $model = Mage::getModel("extendedreview/extendedreview")							
							->setStatus($status)
							->setApprovedBy($adminuUserEmail)
							->setId($id)
							->save();
						if($status==2){
					  		Mage::helper('extendedreview')->sendApprovalEmail($id);  
						}	

				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Status for Item(s) has been successfully updated."));
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
			$fileName   = 'extendedreview.csv';
			$grid       = $this->getLayout()->createBlock('extendedreview/adminhtml_extendedreview_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'extendedreview.xml';
			$grid       = $this->getLayout()->createBlock('extendedreview/adminhtml_extendedreview_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
