<?php

class Iksula_Customerdelete_Adminhtml_CustomerdeleteController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("customerdelete/customerdelete")->_addBreadcrumb(Mage::helper("adminhtml")->__("Customerdelete  Manager"),Mage::helper("adminhtml")->__("Customerdelete Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Customerdelete"));
			    $this->_title($this->__("Manager Customerdelete"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Customerdelete"));
				$this->_title($this->__("Customerdelete"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("customerdelete/customerdelete")->load($id);
				if ($model->getId()) {
					Mage::register("customerdelete_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("customerdelete/customerdelete");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Customerdelete Manager"), Mage::helper("adminhtml")->__("Customerdelete Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Customerdelete Description"), Mage::helper("adminhtml")->__("Customerdelete Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("customerdelete/adminhtml_customerdelete_edit"))->_addLeft($this->getLayout()->createBlock("customerdelete/adminhtml_customerdelete_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("customerdelete")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Customerdelete"));
		$this->_title($this->__("Customerdelete"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("customerdelete/customerdelete")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("customerdelete_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("customerdelete/customerdelete");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Customerdelete Manager"), Mage::helper("adminhtml")->__("Customerdelete Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Customerdelete Description"), Mage::helper("adminhtml")->__("Customerdelete Description"));


		$this->_addContent($this->getLayout()->createBlock("customerdelete/adminhtml_customerdelete_edit"))->_addLeft($this->getLayout()->createBlock("customerdelete/adminhtml_customerdelete_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("customerdelete/customerdelete")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Customerdelete was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setCustomerdeleteData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setCustomerdeleteData($this->getRequest()->getPost());
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
						$model = Mage::getModel("customerdelete/customerdelete");
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
                      $model = Mage::getModel("customerdelete/customerdelete");
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
			$fileName   = 'customerdelete.csv';
			$grid       = $this->getLayout()->createBlock('customerdelete/adminhtml_customerdelete_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'customerdelete.xml';
			$grid       = $this->getLayout()->createBlock('customerdelete/adminhtml_customerdelete_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
