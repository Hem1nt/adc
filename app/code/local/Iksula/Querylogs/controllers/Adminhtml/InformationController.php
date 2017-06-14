<?php

class Iksula_Querylogs_Adminhtml_InformationController extends Mage_Adminhtml_Controller_Action
{
	
	protected function _isAllowed(){
        // return true;
        return Mage::getSingleton('admin/session')->isAllowed('querylogs');  
    }
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("querylogs/information")->_addBreadcrumb(Mage::helper("adminhtml")->__("Information  Manager"),Mage::helper("adminhtml")->__("Information Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Querylogs"));
			    $this->_title($this->__("Manager Information"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Querylogs"));
				$this->_title($this->__("Information"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("querylogs/information")->load($id);
				if ($model->getId()) {
					Mage::register("information_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("querylogs/information");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Information Manager"), Mage::helper("adminhtml")->__("Information Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Information Description"), Mage::helper("adminhtml")->__("Information Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("querylogs/adminhtml_information_edit"))->_addLeft($this->getLayout()->createBlock("querylogs/adminhtml_information_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("querylogs")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Querylogs"));
		$this->_title($this->__("Information"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("querylogs/information")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("information_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("querylogs/information");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Information Manager"), Mage::helper("adminhtml")->__("Information Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Information Description"), Mage::helper("adminhtml")->__("Information Description"));


		$this->_addContent($this->getLayout()->createBlock("querylogs/adminhtml_information_edit"))->_addLeft($this->getLayout()->createBlock("querylogs/adminhtml_information_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("querylogs/information")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Information was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setInformationData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setInformationData($this->getRequest()->getPost());
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
						$model = Mage::getModel("querylogs/information");
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
				$ids = $this->getRequest()->getPost('contact_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("querylogs/information");
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
			$fileName   = 'information.csv';
			$grid       = $this->getLayout()->createBlock('querylogs/adminhtml_information_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'information.xml';
			$grid       = $this->getLayout()->createBlock('querylogs/adminhtml_information_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
