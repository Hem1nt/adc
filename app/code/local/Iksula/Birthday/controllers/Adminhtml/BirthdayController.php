<?php

class Iksula_Birthday_Adminhtml_BirthdayController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("birthday/birthday")->_addBreadcrumb(Mage::helper("adminhtml")->__("Birthday  Manager"),Mage::helper("adminhtml")->__("Birthday Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Birthday"));
			    $this->_title($this->__("Manager Birthday"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Birthday"));
				$this->_title($this->__("Birthday"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("birthday/birthday")->load($id);
				if ($model->getId()) {
					Mage::register("birthday_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("birthday/birthday");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Birthday Manager"), Mage::helper("adminhtml")->__("Birthday Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Birthday Description"), Mage::helper("adminhtml")->__("Birthday Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("birthday/adminhtml_birthday_edit"))->_addLeft($this->getLayout()->createBlock("birthday/adminhtml_birthday_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("birthday")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Birthday"));
		$this->_title($this->__("Birthday"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("birthday/birthday")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("birthday_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("birthday/birthday");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Birthday Manager"), Mage::helper("adminhtml")->__("Birthday Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Birthday Description"), Mage::helper("adminhtml")->__("Birthday Description"));


		$this->_addContent($this->getLayout()->createBlock("birthday/adminhtml_birthday_edit"))->_addLeft($this->getLayout()->createBlock("birthday/adminhtml_birthday_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						
				 //save image
		try{

if((bool)$post_data['birthdate']['delete']==1) {

	        $post_data['birthdate']='';

}
else {

	unset($post_data['birthdate']);

	if (isset($_FILES)){

		if ($_FILES['birthdate']['name']) {

			if($this->getRequest()->getParam("id")){
				$model = Mage::getModel("birthday/birthday")->load($this->getRequest()->getParam("id"));
				if($model->getData('birthdate')){
						$io = new Varien_Io_File();
						$io->rm(Mage::getBaseDir('media').DS.implode(DS,explode('/',$model->getData('birthdate'))));	
				}
			}
						$path = Mage::getBaseDir('media') . DS . 'birthday' . DS .'birthday'.DS;
						$uploader = new Varien_File_Uploader('birthdate');
						$uploader->setAllowedExtensions(array('jpg','png','gif'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);
						$destFile = $path.$_FILES['birthdate']['name'];
						$filename = $uploader->getNewFileName($destFile);
						$uploader->save($path, $filename);

						$post_data['birthdate']='birthday/birthday/'.$filename;
		}
    }
}

        } catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
        }
//save image


						$model = Mage::getModel("birthday/birthday")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Birthday was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setBirthdayData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setBirthdayData($this->getRequest()->getPost());
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
						$model = Mage::getModel("birthday/birthday");
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
				$ids = $this->getRequest()->getPost('birthday_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("birthday/birthday");
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
			$fileName   = 'birthday.csv';
			$grid       = $this->getLayout()->createBlock('birthday/adminhtml_birthday_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'birthday.xml';
			$grid       = $this->getLayout()->createBlock('birthday/adminhtml_birthday_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}

		public function sendCouponAction(){
     	   $this->loadLayout();
     	   $this->renderLayout();
    	}
    	public function sendEmailCouponAction(){
     	   $email = $this->getRequest()->getParam('email');
     	   Mage::helper("birthday/coupon")->sendCoupon($email);
    	}
}
