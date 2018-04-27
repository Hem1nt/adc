<?php

class Manoj_Abandoned_Adminhtml_AbandonedorderController extends Mage_Adminhtml_Controller_Action
{
		protected function _isAllowed()
		{
		//return Mage::getSingleton('admin/session')->isAllowed('abandoned/abandonedorder');
			return true;
		}

		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("abandoned/abandonedorder")->_addBreadcrumb(Mage::helper("adminhtml")->__("Abandonedorder  Manager"),Mage::helper("adminhtml")->__("Abandonedorder Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Abandoned"));
			    $this->_title($this->__("Manager Abandonedorder"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Abandoned"));
				$this->_title($this->__("Abandonedorder"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("abandoned/abandonedorder")->load($id);
				if ($model->getId()) {
					Mage::register("abandonedorder_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("abandoned/abandonedorder");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Abandonedorder Manager"), Mage::helper("adminhtml")->__("Abandonedorder Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Abandonedorder Description"), Mage::helper("adminhtml")->__("Abandonedorder Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("abandoned/adminhtml_abandonedorder_edit"))->_addLeft($this->getLayout()->createBlock("abandoned/adminhtml_abandonedorder_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("abandoned")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Abandoned"));
		$this->_title($this->__("Abandonedorder"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("abandoned/abandonedorder")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("abandonedorder_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("abandoned/abandonedorder");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Abandonedorder Manager"), Mage::helper("adminhtml")->__("Abandonedorder Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Abandonedorder Description"), Mage::helper("adminhtml")->__("Abandonedorder Description"));


		$this->_addContent($this->getLayout()->createBlock("abandoned/adminhtml_abandonedorder_edit"))->_addLeft($this->getLayout()->createBlock("abandoned/adminhtml_abandonedorder_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("abandoned/abandonedorder")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Abandonedorder was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setAbandonedorderData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setAbandonedorderData($this->getRequest()->getPost());
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
						$model = Mage::getModel("abandoned/abandonedorder");
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
				$ids = $this->getRequest()->getPost('abandoned_order_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("abandoned/abandonedorder");
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
			$fileName   = 'abandonedorder.csv';
			$grid       = $this->getLayout()->createBlock('abandoned/adminhtml_abandonedorder_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'abandonedorder.xml';
			$grid       = $this->getLayout()->createBlock('abandoned/adminhtml_abandonedorder_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
