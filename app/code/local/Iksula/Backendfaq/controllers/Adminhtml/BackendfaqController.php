<?php

class Iksula_Backendfaq_Adminhtml_BackendfaqController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("backendfaq/backendfaq")->_addBreadcrumb(Mage::helper("adminhtml")->__("Backendfaq  Manager"),Mage::helper("adminhtml")->__("Backendfaq Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Backendfaq"));
			    $this->_title($this->__("Manager Backendfaq"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Backendfaq"));
				$this->_title($this->__("Backendfaq"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("backendfaq/backendfaq")->load($id);
				if ($model->getId()) {
					Mage::register("backendfaq_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("backendfaq/backendfaq");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Backendfaq Manager"), Mage::helper("adminhtml")->__("Backendfaq Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Backendfaq Description"), Mage::helper("adminhtml")->__("Backendfaq Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("backendfaq/adminhtml_backendfaq_edit"))->_addLeft($this->getLayout()->createBlock("backendfaq/adminhtml_backendfaq_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("backendfaq")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Backendfaq"));
		$this->_title($this->__("Backendfaq"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("backendfaq/backendfaq")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("backendfaq_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("backendfaq/backendfaq");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Backendfaq Manager"), Mage::helper("adminhtml")->__("Backendfaq Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Backendfaq Description"), Mage::helper("adminhtml")->__("Backendfaq Description"));


		$this->_addContent($this->getLayout()->createBlock("backendfaq/adminhtml_backendfaq_edit"))->_addLeft($this->getLayout()->createBlock("backendfaq/adminhtml_backendfaq_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();
			      

				if ($post_data) {

					try {

						

						$model = Mage::getModel("backendfaq/backendfaq")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						$post_data['returnId'] = $model->getId();
					
						Mage::dispatchEvent('faq_save_after', array('data' => $post_data));

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Backendfaq was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setBackendfaqData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setBackendfaqData($this->getRequest()->getPost());
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
						$model = Mage::getModel("backendfaq/backendfaq");
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
                      $model = Mage::getModel("backendfaq/backendfaq");
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
			$fileName   = 'backendfaq.csv';
			$grid       = $this->getLayout()->createBlock('backendfaq/adminhtml_backendfaq_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'backendfaq.xml';
			$grid       = $this->getLayout()->createBlock('backendfaq/adminhtml_backendfaq_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
