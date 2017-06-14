<?php

class Iksula_Prescription_Adminhtml_PrescriptionController extends Mage_Adminhtml_Controller_Action
{
	protected function _isAllowed(){
        // return true;
        return Mage::getSingleton('admin/session')->isAllowed('prescription/prescription');  
    }
    
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("prescription/prescription")->_addBreadcrumb(Mage::helper("adminhtml")->__("Prescription  Manager"),Mage::helper("adminhtml")->__("Prescription Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Prescription"));
			    $this->_title($this->__("Manager Prescription"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Prescription"));
				$this->_title($this->__("Prescription"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("prescription/prescription")->load($id);
				if ($model->getId()) {
					Mage::register("prescription_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("prescription/prescription");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Prescription Manager"), Mage::helper("adminhtml")->__("Prescription Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Prescription Description"), Mage::helper("adminhtml")->__("Prescription Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("prescription/adminhtml_prescription_edit"))->_addLeft($this->getLayout()->createBlock("prescription/adminhtml_prescription_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("prescription")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Prescription"));
		$this->_title($this->__("Prescription"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("prescription/prescription")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("prescription_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("prescription/prescription");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Prescription Manager"), Mage::helper("adminhtml")->__("Prescription Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Prescription Description"), Mage::helper("adminhtml")->__("Prescription Description"));


		$this->_addContent($this->getLayout()->createBlock("prescription/adminhtml_prescription_edit"))->_addLeft($this->getLayout()->createBlock("prescription/adminhtml_prescription_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("prescription/prescription")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Prescription was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setPrescriptionData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setPrescriptionData($this->getRequest()->getPost());
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
						$model = Mage::getModel("prescription/prescription");
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
                      $model = Mage::getModel("prescription/prescription");
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
			$fileName   = 'prescription.csv';
			$grid       = $this->getLayout()->createBlock('prescription/adminhtml_prescription_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'prescription.xml';
			$grid       = $this->getLayout()->createBlock('prescription/adminhtml_prescription_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
