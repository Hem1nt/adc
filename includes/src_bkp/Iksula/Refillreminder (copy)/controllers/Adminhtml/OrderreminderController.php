<?php

class Iksula_Refillreminder_Adminhtml_OrderreminderController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("refillreminder/orderreminder")->_addBreadcrumb(Mage::helper("adminhtml")->__("Refillreminder  Manager"),Mage::helper("adminhtml")->__("Refillreminder Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Orderreminder"));
			    $this->_title($this->__("Manager Orderreminder"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Orderreminder"));
				$this->_title($this->__("Orderreminder"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("refillreminder/orderreminder")->load($id);
				if ($model->getId()) {
					Mage::register("orderreminder_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("refillreminder/orderreminder");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Orderreminder Manager"), Mage::helper("adminhtml")->__("Orderreminder Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Orderreminder Description"), Mage::helper("adminhtml")->__("Orderreminder Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("refillreminder/adminhtml_orderreminder_edit"))->_addLeft($this->getLayout()->createBlock("refillreminder/adminhtml_orderreminder_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("refillreminder")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Refillreminder"));
		$this->_title($this->__("Refillreminder"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("refillreminder/orderreminder")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("orderreminder_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("refillreminder/orderreminder");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Orderreminder Manager"), Mage::helper("adminhtml")->__("Orderreminder Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Orderreminder Description"), Mage::helper("adminhtml")->__("Orderreminder Description"));


		$this->_addContent($this->getLayout()->createBlock("refillreminder/adminhtml_orderreminder_edit"))->_addLeft($this->getLayout()->createBlock("refillreminder/adminhtml_orderreminder_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {
					if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
					
					try {	
				
				/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					// Only *.csv extention would work
	           		$uploader->setAllowedExtensions(array('csv'));
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS ."refillreminder" .DS ;
					$uploader->save($path, $_FILES['filename']['name'] );
					$csv = new Varien_File_Csv();
					$dataCollection = $csv->getData($path.$_FILES['filename']['name']); //path to csv
					array_shift($dataCollection);
				
				} catch (Exception $e) {
		      
		        }
				
		        $model_refillreminder = Mage::getModel("refillreminder/orderreminder");
		        foreach($dataCollection as $_data){

		        	$new_data['id'] = $_data[0];
		        	$new_data['comment'] = $_data[1];


		        	$model_refillreminder->load($new_data['id'])->setData('comment',$new_data['comment']);

		        	$model_refillreminder->save();

		        } 


			        Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Orderreminder was successfully saved"));
			        $this->_redirect("*/*/");
			        return;	
			        exit;
			    }

			    try {

						
					$post_data['remind_flag']=implode(',',$post_data['remind_flag']);

						$model = Mage::getModel("refillreminder/orderreminder")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Orderreminder was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setRefillreminderData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setRefillreminderData($this->getRequest()->getPost());
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
						$model = Mage::getModel("refillreminder/orderreminder");
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
				$ids = $this->getRequest()->getPost('order_inc_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("refillreminder/orderreminder");
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
			$fileName   = 'orderreminder.csv';
			$grid       = $this->getLayout()->createBlock('refillreminder/adminhtml_orderreminder_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'orderreminder.xml';
			$grid       = $this->getLayout()->createBlock('refillreminder/adminhtml_orderreminder_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
