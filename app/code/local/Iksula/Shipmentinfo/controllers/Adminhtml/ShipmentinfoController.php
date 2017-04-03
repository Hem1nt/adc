<?php

class Iksula_Shipmentinfo_Adminhtml_ShipmentinfoController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("shipmentinfo/shipmentinfo")->_addBreadcrumb(Mage::helper("adminhtml")->__("Shipmentinfo  Manager"),Mage::helper("adminhtml")->__("Shipmentinfo Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Shipmentinfo"));
			    $this->_title($this->__("Manager Shipmentinfo"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Shipmentinfo"));
				$this->_title($this->__("Shipmentinfo"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("shipmentinfo/shipmentinfo")->load($id);
				if ($model->getId()) {
					Mage::register("shipmentinfo_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("shipmentinfo/shipmentinfo");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Shipmentinfo Manager"), Mage::helper("adminhtml")->__("Shipmentinfo Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Shipmentinfo Description"), Mage::helper("adminhtml")->__("Shipmentinfo Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("shipmentinfo/adminhtml_shipmentinfo_edit"))->_addLeft($this->getLayout()->createBlock("shipmentinfo/adminhtml_shipmentinfo_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("shipmentinfo")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Shipmentinfo"));
		$this->_title($this->__("Shipmentinfo"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("shipmentinfo/shipmentinfo")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("shipmentinfo_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("shipmentinfo/shipmentinfo");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Shipmentinfo Manager"), Mage::helper("adminhtml")->__("Shipmentinfo Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Shipmentinfo Description"), Mage::helper("adminhtml")->__("Shipmentinfo Description"));


		$this->_addContent($this->getLayout()->createBlock("shipmentinfo/adminhtml_shipmentinfo_edit"))->_addLeft($this->getLayout()->createBlock("shipmentinfo/adminhtml_shipmentinfo_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("shipmentinfo/shipmentinfo")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Shipmentinfo was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setShipmentinfoData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setShipmentinfoData($this->getRequest()->getPost());
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
						$model = Mage::getModel("shipmentinfo/shipmentinfo");
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
				$ids = $this->getRequest()->getPost('entity_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("shipmentinfo/shipmentinfo");
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
			$fileName   = 'shipmentinfo.csv';
			$grid       = $this->getLayout()->createBlock('shipmentinfo/adminhtml_shipmentinfo_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'shipmentinfo.xml';
			$grid       = $this->getLayout()->createBlock('shipmentinfo/adminhtml_shipmentinfo_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
