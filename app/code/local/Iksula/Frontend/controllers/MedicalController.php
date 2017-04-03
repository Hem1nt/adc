<?php
class Iksula_Frontend_MedicalController extends Mage_Core_Controller_Front_Action{
   public function indexAction()
    {
     
        $physicianname = $this->getRequest()->getParam('physicianname');
        $physiciantelephone = $this->getRequest()->getParam('physiciantelephone');
        $physicianfax = $this->getRequest()->getParam('physicianfax');
        $drugallergies = $this->getRequest()->getParam('drugallergies');
        $currentmedications = $this->getRequest()->getParam('currentmedications');
        $currenttreatments = $this->getRequest()->getParam('currenttreatments');
        $smoke = $this->getRequest()->getParam('smoke');
        $drink = $this->getRequest()->getParam('drink');
        $pregnant = $this->getRequest()->getParam('pregnant');

        Mage::getSingleton('checkout/session')->setPhysicianname($physicianname);
        Mage::getSingleton('checkout/session')->setPhysiciantelephone($physiciantelephone);
        Mage::getSingleton('checkout/session')->setPhysicianfax($physicianfax);
        Mage::getSingleton('checkout/session')->setDrugallergies($drugallergies);
        Mage::getSingleton('checkout/session')->setCurrentmedications($currentmedications);
        Mage::getSingleton('checkout/session')->setCurrenttreatments($currenttreatments);
        Mage::getSingleton('checkout/session')->setSmoke($smoke);
        Mage::getSingleton('checkout/session')->setDrink($drink);
        Mage::getSingleton('checkout/session')->setPregnant($pregnant);
        
    }

    public function callforoffersregAction()
    {
    //Mage::getSingleton('checkout/session')->unsCallforfreeval();
    $callforfreeval = Mage::app()->getRequest()->getParam('callforfree');

    $timetocall = Mage::app()->getRequest()->getParam('timetocall');

    Mage::getSingleton('checkout/session')->setCallforfreeval($callforfreeval);
    Mage::getSingleton('checkout/session')->setTimetocall($timetocall);

    }
    public function checkaddressAction()
    {
       $addressid = Mage::app()->getRequest()->getParam('addressid');

       if($addressid){
          $addressdata = Mage::getModel('customer/address')->load($addressid);
          $region = $addressdata->getData('region');
          if($region){
            echo 'true';
          }else{
            echo 'false';
          }
       }else{
        echo 'true';
       }
    }
    public function uploadAction()
    {
      // echo "filename----";
      $type = 'file';
      if(isset($_FILES[$type]['name']) && $_FILES[$type]['name'] != '') {
        try {
            $path = Mage::getBaseDir('media').DS.'uploads'.DS;  //desitnation directory     
            $fname = $_FILES[$type]['name']; //file name                        
            $uploader = new Varien_File_Uploader($type); //load class
            $uploader->setAllowedExtensions(array('doc','pdf','txt','docx','jpg','png','jpeg')); //Allowed extension for file
            $uploader->setAllowCreateFolders(true); //for creating the directory if not exists
            $uploader->setAllowRenameFiles(true); //if true, uploaded file's name will be changed, if file with the same name already exists directory.
            $uploader->setFilesDispersion(false);
            $uploader->save($path,$fname); //save the file on the specified path
          $filename = $uploader->getUploadedFileName();
                         
        } 
        catch (Exception $e) {
            echo 'Error Message: '.$e->getMessage();
        }   
      }
      Mage::getSingleton('checkout/session')->setPrescription($filename);
      echo $filename;
    }
}
