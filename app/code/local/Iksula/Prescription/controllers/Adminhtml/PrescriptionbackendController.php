<?php
class Iksula_Prescription_Adminhtml_PrescriptionbackendController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Prescription"));
	   $this->renderLayout();
    }

    public function bulkPrescripionImportAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Prescription"));
	   $this->renderLayout();
    }

    public function bulkPrescripionOrderIdImportAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Prescription"));
	   $this->renderLayout();
    }

    public function bulkOrderIdImportAction(){
        // foreach($_FILES['orderid_import']['name']  as $key =>$image){
    	$errorCunter = 0;
	        try
		    {      
		        $path = Mage::getBaseDir('var').DS.'import'.DS;      
		        $uploader = new Varien_File_Uploader('orderid_import');
				// $directory = date('d-m-Y');
				// $directoryPath = Mage::getBaseDir('var').'/Prescription/'.$directory.'/';
				// if (!file_exists($directoryPath)) {
				//     mkdir($directoryPath, 0777, true);
				// }
		        $uploader->setAllowedExtensions(array('csv','xls')); 
		        $uploader->setAllowCreateFolders(true); 
		        $uploader->setAllowRenameFiles(false); 
		        $uploader->setFilesDispersion(false);
		        $fname =  $_FILES['orderid_import']['name'];                       
		        $uploader->save($path,$fname); 
		        $fileName = $uploader->getUploadedFileName();
		        // $scanned_directory = array_diff(scandir(Mage::getBaseDir('var').'/Prescription/'), array('..', '.'));
		        $csv = new Varien_File_Csv();
		        $csvData = $csv->getData($path.$fileName);
		        foreach ($csvData as $key => $value) {
		        	if($key == 0){
		        		continue;
		        	}

		        	$OrderData =  Mage::getModel('sales/order')->loadByIncrementId($value[0]);
		        	$prescriptionModel = Mage::getModel('prescription/prescription');
		        	$orderGetData = $OrderData->getData();
		        	if($orderGetData){
						$arrayData = array(
							'medical_history' => $OrderData->getData('medical_history'),
							'order_id' => $OrderData->getData('increment_id'),
							'customer_name' => $OrderData->getData('customer_firstname').' '.$OrderData->getData('customer_lastname'),
							'primary_physicians_name' => $OrderData->getData('physicianname'),
							'physicians_telephone_no' => $OrderData->getData('physiciantelephone'),
							'drug_allergies' => $OrderData->getData('drug_allergies'),
							'current_medications_allergies' => $OrderData->getData('current_medications'),
							'current_treatments' => $OrderData->getData('current_treatments'),
							'smoke' => $OrderData->getData('smoke'),
							'drink' => $OrderData->getData('drink')
						);
			        	$prescriptionModel->setData($arrayData)->save();
		        	}	
			        	$arrayData = array();
		        }
		    }
		    catch (Exception $e)
		    {
		        $errorCunter++;
		        $message = 'Error Message: '.$e->getMessage();
		        Mage::getSingleton('adminhtml/session')->addError($message); 
				Mage::app()->getResponse()->setRedirect($this->getUrl("prescription/adminhtml_prescriptionbackend/index"));
		    }
		// }
		if(!$errorCunter){
			Mage::getSingleton('adminhtml/session')->addSuccess('Files Successfully Upload'); 
			Mage::app()->getResponse()->setRedirect($this->getUrl("prescription/adminhtml_prescriptionbackend/index"));   
		}    
    }

    public function bulkPrescripionFileImportAction(){
    	$errorCunter = 0;
        foreach($_FILES['orderid_import']['name']  as $key =>$image){
	        try
		    {      
		        $path = Mage::getBaseDir('var').DS.'import'.DS;      
		        $uploader = new Varien_File_Uploader(
				        array(
				    'name' => $_FILES['orderid_import']['name'][$key],
				    'type' => $_FILES['orderid_import']['type'][$key],
				    'tmp_name' => $_FILES['orderid_import']['tmp_name'][$key],
				    'error' => $_FILES['orderid_import']['error'][$key],
				    'size' => $_FILES['orderid_import']['size'][$key]
				        )
				);
				// $directory = date('d-m-Y');
				// $directoryPath = Mage::getBaseDir('var').'/Prescription/'.$directory.'/';
				// if (!file_exists($directoryPath)) {
				//     mkdir($directoryPath);
				// }
				$directoryPath = Mage::getBaseDir('media').'/Prescription/';
		        $uploader->setAllowedExtensions(array('csv','xls','jpg','png','pdf','jpeg','docs')); 
		        $uploader->setAllowCreateFolders(true); 
		        $uploader->setAllowRenameFiles(false); 
		        $uploader->setFilesDispersion(false);
		        $fname =  $_FILES['orderid_import']['name'][$key];                       
		        $uploader->save($directoryPath,$fname); 
		        $fileName = $uploader->getUploadedFileName();
		        // $scanned_directory = array_diff(scandir(Mage::getBaseDir('var').'/Prescription/'), array('..', '.'));
		        // $csv = new Varien_File_Csv();
		        // $csvData = $csv->getData($directoryPath.$fileName);
		    }
		    catch (Exception $e)
		    {
		        $errorCunter++;
		        $message = 'Error Message: '.$e->getMessage();
		        Mage::getSingleton('adminhtml/session')->addError($message); 
				Mage::app()->getResponse()->setRedirect($this->getUrl("prescription/adminhtml_prescriptionbackend/bulkPrescripionImport"));
		    }
		}
		if(!$errorCunter){
			Mage::getSingleton('adminhtml/session')->addSuccess('Files Successfully Upload'); 
			Mage::app()->getResponse()->setRedirect($this->getUrl("prescription/adminhtml_prescriptionbackend/bulkPrescripionImport"));   
		}
    }
    public function  bulkPrescripionFileOrderIdImportAction(){
        $errorCunter = 0;
        try
		    {      
		        $path = Mage::getBaseDir('var').DS.'import'.DS;      
		        $uploader = new Varien_File_Uploader('orderid_import');
		        $uploader->setAllowedExtensions(array('csv','xls')); 
		        $uploader->setAllowCreateFolders(true); 
		        $uploader->setAllowRenameFiles(false); 
		        $uploader->setFilesDispersion(false);
		        $fname =  $_FILES['orderid_import']['name'];                       
		        $uploader->save($path,$fname); 
		        $fileName = $uploader->getUploadedFileName();
		        // $scanned_directory = array_diff(scandir(Mage::getBaseDir('var').'/Prescription/'), array('..', '.'));
		        $csv = new Varien_File_Csv();
		        $csvData = $csv->getData($path.$fileName);
		        foreach ($csvData as $key => $value) {
		        	if($key == 0){
		        		continue;
		        	}
		        	$prescriptionModel = Mage::getModel('prescription/prescription')->getCollection()->addFieldToFilter('order_id',$value[0]);
		        	foreach ($prescriptionModel->getData() as $key) {
		        		$id = $key['id'];
		        		Mage::getModel('prescription/prescription')->load($id)->setPrescription($value[1])->save();
		        	}
		        }
		    }
		    catch (Exception $e)
		    {
		        $errorCunter++;
		        $message = 'Error Message: '.$e->getMessage();
		        Mage::getSingleton('adminhtml/session')->addError($message); 
				Mage::app()->getResponse()->setRedirect($this->getUrl("prescription/adminhtml_prescriptionbackend/bulkPrescripionOrderIdImport"));
		    }
		if(!$errorCunter){
			Mage::getSingleton('adminhtml/session')->addSuccess('Files Successfully Upload'); 
			Mage::app()->getResponse()->setRedirect($this->getUrl("prescription/adminhtml_prescriptionbackend/bulkPrescripionOrderIdImport"));   
		}    
    }

    public function DownloadFileAction(){
    	$file = $this->getRequest()->getParam('filename');
		$link = Mage::getBaseDir('media').'/Prescription/'.$file;
		if(!$link){ // file does not exist
		    die('file not found');
		} else {
		    header("Cache-Control: public");
		    header("Content-Description: File Transfer");
		    header("Content-Disposition: attachment; filename=$file");
		    header("Content-Type: application/zip");
		    header("Content-Transfer-Encoding: binary");

		    // read the file from disk
		    readfile($link);
		}
    }
}