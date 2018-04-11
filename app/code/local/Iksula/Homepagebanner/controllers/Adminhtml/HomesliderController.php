<?php

class Iksula_Homepagebanner_Adminhtml_HomesliderController extends Mage_Adminhtml_Controller_Action
{
	

	protected function _isAllowed(){
	        // return true;
		return Mage::getSingleton('admin/session')->isAllowed('homepagebanner');  
	}

	protected function _initAction()
	{
		$this->loadLayout()->_setActiveMenu("homepagebanner/homeslider")->_addBreadcrumb(Mage::helper("adminhtml")->__("Homeslider  Manager"),Mage::helper("adminhtml")->__("Homeslider Manager"));
		return $this;
	}
	public function indexAction() 
	{
		$this->_title($this->__("Homepagebanner"));
		$this->_title($this->__("Manager Homeslider"));

		$this->_initAction();
		$this->renderLayout();
	}
	public function editAction()
	{			    
		$this->_title($this->__("Homepagebanner"));
		$this->_title($this->__("Homeslider"));
		$this->_title($this->__("Edit Item"));

		$id = $this->getRequest()->getParam("id");
		$model = Mage::getModel("homepagebanner/homeslider")->load($id);
		if ($model->getId()) {
			Mage::register("homeslider_data", $model);
			$this->loadLayout();
			$this->_setActiveMenu("homepagebanner/homeslider");
			$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Homeslider Manager"), Mage::helper("adminhtml")->__("Homeslider Manager"));
			$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Homeslider Description"), Mage::helper("adminhtml")->__("Homeslider Description"));
			$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock("homepagebanner/adminhtml_homeslider_edit"))->_addLeft($this->getLayout()->createBlock("homepagebanner/adminhtml_homeslider_edit_tabs"));
			$this->renderLayout();
		} 
		else {
			Mage::getSingleton("adminhtml/session")->addError(Mage::helper("homepagebanner")->__("Item does not exist."));
			$this->_redirect("*/*/");
		}
	}

	public function newAction()
	{

		$this->_title($this->__("Homepagebanner"));
		$this->_title($this->__("Homeslider"));
		$this->_title($this->__("New Item"));

		$id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("homepagebanner/homeslider")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("homeslider_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("homepagebanner/homeslider");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Homeslider Manager"), Mage::helper("adminhtml")->__("Homeslider Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Homeslider Description"), Mage::helper("adminhtml")->__("Homeslider Description"));


		$this->_addContent($this->getLayout()->createBlock("homepagebanner/adminhtml_homeslider_edit"))->_addLeft($this->getLayout()->createBlock("homepagebanner/adminhtml_homeslider_edit_tabs"));

		$this->renderLayout();

	}
	public function saveAction()
	{

		$post_data=$this->getRequest()->getPost();


		if ($post_data) {

			try {


				 //save image
				try{

					if((bool)$post_data['image']['delete']==1) {

						$post_data['image']='';

					}
					else {

						unset($post_data['image']);

						if (isset($_FILES)){

							if ($_FILES['image']['name']) {

								if($this->getRequest()->getParam("id")){
									$model = Mage::getModel("homepagebanner/homeslider")->load($this->getRequest()->getParam("id"));
									if($model->getData('image')){
										$io = new Varien_Io_File();
										$io->rm(Mage::getBaseDir('media').DS.implode(DS,explode('/',$model->getData('image'))));	
									}
								}
								$path = Mage::getBaseDir('media') . DS . 'homepagebanner' . DS .'homeslider'.DS;
								$uploader = new Varien_File_Uploader('image');
								$uploader->setAllowedExtensions(array('jpg','png','gif'));
								$uploader->setAllowRenameFiles(false);
								$uploader->setFilesDispersion(false);
								$destFile = $path.$_FILES['image']['name'];
								$filename = $uploader->getNewFileName($destFile);
								$uploader->save($path, $filename);

								$post_data['image']='homepagebanner/homeslider/'.$filename;
							}
						}
					}

				} catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
					return;
				}
//save image
				/*Deactive Date Active Date Attribute Comparison  */    
				if($post_data['deactive_date']) {   
					$deactiveDate = strtotime($post_data['deactive_date']);
					$activeDate = strtotime($post_data['active_date']);
					if($deactiveDate > $activeDate){
						$model = Mage::getModel("homepagebanner/homeslider")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Homeslider was successfully saved with Deactive Date"));
						Mage::getSingleton("adminhtml/session")->setHomesliderData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 

					else{
						$error = "Deactive date cannot be less than or equal to active date";
						Mage::getSingleton("adminhtml/session")->addError($error);
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
						return;	
					}
				}
				else{
					$model = Mage::getModel("homepagebanner/homeslider")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Homeslider was successfully saved without Deactive Date"));
						Mage::getSingleton("adminhtml/session")->setHomesliderData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
				}
				/*Deactive Date Active Date Attribute Comparison  */    
			}
				catch (Exception $e) {
					Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
					Mage::getSingleton("adminhtml/session")->setHomesliderData($this->getRequest()->getPost());
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
					$model = Mage::getModel("homepagebanner/homeslider");
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
					$model = Mage::getModel("homepagebanner/homeslider");
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
			$fileName   = 'homeslider.csv';
			$grid       = $this->getLayout()->createBlock('homepagebanner/adminhtml_homeslider_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'homeslider.xml';
			$grid       = $this->getLayout()->createBlock('homepagebanner/adminhtml_homeslider_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
	}
