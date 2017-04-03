<?php

class Iksula_Refillreminder_Adminhtml_RefillreminderController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("refillreminder/refillreminder")->_addBreadcrumb(Mage::helper("adminhtml")->__("Refillreminder  Manager"),Mage::helper("adminhtml")->__("Refillreminder Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Refillreminder"));
			    $this->_title($this->__("Manager Refillreminder"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Refillreminder"));
				$this->_title($this->__("Refillreminder"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("refillreminder/refillreminder")->load($id);
				if ($model->getId()) {
					Mage::register("refillreminder_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("refillreminder/refillreminder");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Refillreminder Manager"), Mage::helper("adminhtml")->__("Refillreminder Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Refillreminder Description"), Mage::helper("adminhtml")->__("Refillreminder Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("refillreminder/adminhtml_refillreminder_edit"))->_addLeft($this->getLayout()->createBlock("refillreminder/adminhtml_refillreminder_edit_tabs"));
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
		$model  = Mage::getModel("refillreminder/refillreminder")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("refillreminder_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("refillreminder/refillreminder");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Refillreminder Manager"), Mage::helper("adminhtml")->__("Refillreminder Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Refillreminder Description"), Mage::helper("adminhtml")->__("Refillreminder Description"));


		$this->_addContent($this->getLayout()->createBlock("refillreminder/adminhtml_refillreminder_edit"))->_addLeft($this->getLayout()->createBlock("refillreminder/adminhtml_refillreminder_edit_tabs"));

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
					echo $path = Mage::getBaseDir('media') . DS ."refillreminder" .DS ;
					$uploader->save($path, $_FILES['filename']['name'] );
					$csv = new Varien_File_Csv();
					$dataCollection = $csv->getData($path.$_FILES['filename']['name']); //path to csv
					array_shift($dataCollection);
				//echo "<pre>";
				//print_r($dataCollection);
			
					
					
				} catch (Exception $e) {
		      
		        }
					//echo $_FILES['filename']['name'];
					//$helperdata = Mage::helper("check");
				 	$model_refillreminder = Mage::getModel("refillreminder/refillreminder");
					foreach($dataCollection as $_data){
					
								//	print_r($_data);
									//exit;
							//$helperdata->_updateStocks($_data);
													
						//	$helperdata->_addDataProduct($_data);
						
						$new_data['id'] = $_data[0];
						$new_data['comment'] = $_data[1];
						
						//	$new_data['eastzone'] =$_data[6];
								
					//	echo "i m here";
							
							$model_refillreminder->load($new_data['id'])->setData('comment',$new_data['comment']);
												
							$model_refillreminder->save();
								
						} 
				
					
					Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Shipment was successfully saved"));
					$this->_redirect("*/*/");
						return;	
						exit;
					}



					try {

						
					$post_data['remind_flag']=implode(',',$post_data['remind_flag']);

						$model = Mage::getModel("refillreminder/refillreminder")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Refillreminder was successfully saved"));
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
						$model = Mage::getModel("refillreminder/refillreminder");
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
				$ids = $this->getRequest()->getPost('reminder_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("refillreminder/refillreminder");
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
			$fileName   = 'refillreminder.csv';
			$grid       = $this->getLayout()->createBlock('refillreminder/adminhtml_refillreminder_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'refillreminder.xml';
			$grid       = $this->getLayout()->createBlock('refillreminder/adminhtml_refillreminder_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
