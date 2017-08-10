<?php

class Iksula_Echecksteps_Adminhtml_EcheckstepsController extends Mage_Adminhtml_Controller_Action
{
		protected function _isAllowed()
		{
		//return Mage::getSingleton('admin/session')->isAllowed('echecksteps/echecksteps');
			return true;
		}

		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("echecksteps/echecksteps")->_addBreadcrumb(Mage::helper("adminhtml")->__("Echecksteps  Manager"),Mage::helper("adminhtml")->__("Echecksteps Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Echecksteps"));
			    $this->_title($this->__("Manager Echecksteps"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Echecksteps"));
				$this->_title($this->__("Echecksteps"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("echecksteps/echecksteps")->load($id);
				if ($model->getId()) {
					Mage::register("echecksteps_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("echecksteps/echecksteps");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Echecksteps Manager"), Mage::helper("adminhtml")->__("Echecksteps Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Echecksteps Description"), Mage::helper("adminhtml")->__("Echecksteps Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("echecksteps/adminhtml_echecksteps_edit"))->_addLeft($this->getLayout()->createBlock("echecksteps/adminhtml_echecksteps_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("echecksteps")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Echecksteps"));
		$this->_title($this->__("Echecksteps"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("echecksteps/echecksteps")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("echecksteps_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("echecksteps/echecksteps");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Echecksteps Manager"), Mage::helper("adminhtml")->__("Echecksteps Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Echecksteps Description"), Mage::helper("adminhtml")->__("Echecksteps Description"));


		$this->_addContent($this->getLayout()->createBlock("echecksteps/adminhtml_echecksteps_edit"))->_addLeft($this->getLayout()->createBlock("echecksteps/adminhtml_echecksteps_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						
				 //save image
		try{

if((bool)$post_data['image_name']['delete']==1) {

	        $post_data['image_name']='';

}
else {

	unset($post_data['image_name']);

	if (isset($_FILES)){

		if ($_FILES['image_name']['name']) {

			if($this->getRequest()->getParam("id")){
				$model = Mage::getModel("echecksteps/echecksteps")->load($this->getRequest()->getParam("id"));
				if($model->getData('image_name')){
						$io = new Varien_Io_File();
						$io->rm(Mage::getBaseDir('media').DS.implode(DS,explode('/',$model->getData('image_name'))));	
				}
			}
						$path = Mage::getBaseDir('media') . DS . 'echecksteps' . DS .'echecksteps'.DS;
						$uploader = new Varien_File_Uploader('image_name');
						$uploader->setAllowedExtensions(array('jpg','png','gif'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);
						$destFile = $path.$_FILES['image_name']['name'];
						$filename = $uploader->getNewFileName($destFile);
						$uploader->save($path, $filename);

						$post_data['image_name']='echecksteps/echecksteps/'.$filename;
		}
    }
}

        } catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
        }
//save image


						$model = Mage::getModel("echecksteps/echecksteps")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Echecksteps was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setEcheckstepsData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setEcheckstepsData($this->getRequest()->getPost());
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
						$model = Mage::getModel("echecksteps/echecksteps");
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
                      $model = Mage::getModel("echecksteps/echecksteps");
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
			$fileName   = 'echecksteps.csv';
			$grid       = $this->getLayout()->createBlock('echecksteps/adminhtml_echecksteps_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'echecksteps.xml';
			$grid       = $this->getLayout()->createBlock('echecksteps/adminhtml_echecksteps_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
