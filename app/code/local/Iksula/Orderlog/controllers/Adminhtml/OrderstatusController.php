<?php

class Iksula_Orderlog_Adminhtml_OrderstatusController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("orderlog/orderstatus")->_addBreadcrumb(Mage::helper("adminhtml")->__("Orderstatus  Manager"),Mage::helper("adminhtml")->__("Orderstatus Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Orderlog"));
			    $this->_title($this->__("Manager Orderstatus"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Orderlog"));
				$this->_title($this->__("Orderstatus"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("orderlog/orderstatus")->load($id);
				if ($model->getId()) {
					Mage::register("orderstatus_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("orderlog/orderstatus");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Orderstatus Manager"), Mage::helper("adminhtml")->__("Orderstatus Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Orderstatus Description"), Mage::helper("adminhtml")->__("Orderstatus Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("orderlog/adminhtml_orderstatus_edit"))->_addLeft($this->getLayout()->createBlock("orderlog/adminhtml_orderstatus_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("orderlog")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Orderlog"));
		$this->_title($this->__("Orderstatus"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("orderlog/orderstatus")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("orderstatus_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("orderlog/orderstatus");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Orderstatus Manager"), Mage::helper("adminhtml")->__("Orderstatus Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Orderstatus Description"), Mage::helper("adminhtml")->__("Orderstatus Description"));


		$this->_addContent($this->getLayout()->createBlock("orderlog/adminhtml_orderstatus_edit"))->_addLeft($this->getLayout()->createBlock("orderlog/adminhtml_orderstatus_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("orderlog/orderstatus")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Orderstatus was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setOrderstatusData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setOrderstatusData($this->getRequest()->getPost());
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
						$model = Mage::getModel("orderlog/orderstatus");
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
                      $model = Mage::getModel("orderlog/orderstatus");
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
			$fileName   = 'orderstatus.csv';
			$grid       = $this->getLayout()->createBlock('orderlog/adminhtml_orderstatus_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'orderstatus.xml';
			$grid       = $this->getLayout()->createBlock('orderlog/adminhtml_orderstatus_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
