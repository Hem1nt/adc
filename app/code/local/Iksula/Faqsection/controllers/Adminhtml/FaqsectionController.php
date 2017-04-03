<?php

class Iksula_Faqsection_Adminhtml_FaqsectionController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("faqsection/faqsection")->_addBreadcrumb(Mage::helper("adminhtml")->__("Faqsection  Manager"),Mage::helper("adminhtml")->__("Faqsection Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Faqsection"));
			    $this->_title($this->__("Manager Faqsection"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Faqsection"));
				$this->_title($this->__("Faqsection"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("faqsection/faqsection")->load($id);
				if ($model->getId()) {
					Mage::register("faqsection_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("faqsection/faqsection");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Faqsection Manager"), Mage::helper("adminhtml")->__("Faqsection Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Faqsection Description"), Mage::helper("adminhtml")->__("Faqsection Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("faqsection/adminhtml_faqsection_edit"))->_addLeft($this->getLayout()->createBlock("faqsection/adminhtml_faqsection_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("faqsection")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Faqsection"));
		$this->_title($this->__("Faqsection"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("faqsection/faqsection")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("faqsection_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("faqsection/faqsection");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Faqsection Manager"), Mage::helper("adminhtml")->__("Faqsection Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Faqsection Description"), Mage::helper("adminhtml")->__("Faqsection Description"));


		$this->_addContent($this->getLayout()->createBlock("faqsection/adminhtml_faqsection_edit"))->_addLeft($this->getLayout()->createBlock("faqsection/adminhtml_faqsection_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("faqsection/faqsection")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Faqsection was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setFaqsectionData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setFaqsectionData($this->getRequest()->getPost());
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
						$model = Mage::getModel("faqsection/faqsection");
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
				$ids = $this->getRequest()->getPost('sections_typeids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("faqsection/faqsection");
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
			$fileName   = 'faqsection.csv';
			$grid       = $this->getLayout()->createBlock('faqsection/adminhtml_faqsection_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'faqsection.xml';
			$grid       = $this->getLayout()->createBlock('faqsection/adminhtml_faqsection_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
