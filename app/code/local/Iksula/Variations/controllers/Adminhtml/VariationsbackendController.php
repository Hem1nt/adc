<?php
class Iksula_Variations_Adminhtml_VariationsbackendController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Bulk Variation Upload"));
	   $this->renderLayout();
    }

    public function submitAction()
        {
            $post_data=$this->getRequest()->getPost();
            if ($post_data) {
                try {
                    try{
                        if((bool)$post_data['fileupload']['delete']==1) {
                            $post_data['fileupload']='';
                        }
                        else {
                            //unset($post_data['fileupload']);
                            if (isset($_FILES)){
            
                                if (isset($_FILES['fileupload']['name'])) {
                                    if($this->getRequest()->getParam("id")){
                                        $model = Mage::getModel("variations/variations")->load($this->getRequest()->getParam("id"));
                                        if($model->getData('fileupload')){
                                            $io = new Varien_Io_File();
                                            $io->rm(Mage::getBaseDir('media').DS.implode(DS,explode('/',$model->getData('fileupload'))));   
                                        }
                                    }
                                    $path = Mage::getBaseDir('media') . DS . 'variations' . DS .'variations'.DS;
                                    $uploader = new Varien_File_Uploader('fileupload');
                                    $uploader->setAllowedExtensions(array('csv'));
                                    $uploader->setAllowRenameFiles(false);
                                    $uploader->setFilesDispersion(false);
                                    $curDate = date('ymdhis').'.csv';
                                    $destFile = $path.$curDate;
                                    $filename = $uploader->getNewFileName($destFile);
                                    $uploader->save($path, $filename);
                                    $post_data['fileupload']='variations/variations/'.$filename;
                                    $csv = new Varien_File_Csv();
                                    $dataCollection = $csv->getData($path.$curDate); //path to csv

                                }
                            }
                        }

                    } 
                    
                    catch (Exception $e) {
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                        $this->_redirect('*/*/', array('id' => $this->getRequest()->getParam('id')));
                        return;
                    }
                    
                $model_variations = Mage::getModel("variations/variations");
                foreach($dataCollection as $key=>$_data){
                    if($key>0){
                        if(!in_array($_data,$duplicateRow)){
                            $new_data['product_sku'] = $_data[0];
                            $new_data['variations_strength'] = $_data[1];
                            $new_data['variations_url'] = $_data[2];
                            $new_data['sort_order'] = $_data[3];
                            $model_variations->setData($new_data);
                            $model_variations->save();
                            $duplicateRow[] = $_data;  
                        }
                    }
                }
              
                /*$new_post_data = array();
                if($this->getRequest()->getParam("pincode")!=''){
                    // print_r($post_data['']);
                    
                    $new_post_data['pincode'] = $post_data['pincode'];
                    $new_post_data['area'] = $post_data['area'];
                    $new_post_data['location'] = $post_data['location'];
                    $new_post_data['city'] = $post_data['city'];
                    $new_post_data['state'] = $post_data['state'];
                    $new_post_data['code'] = $post_data['code'];
                    $new_post_data['zone'] = $post_data['zone'];
                    $new_post_data['cod'] = implode(',', $post_data['cod']);
                    $new_post_data['prepaid'] = implode(',', $post_data['prepaid']);
                    $new_post_data['status'] = $post_data['status'];
                    // print_r($new_post_data);
                    // exit;
                        $model = Mage::getModel("variations/variations")
                        ->addData($new_post_data)
                        ->setId($this->getRequest()->getParam("id"))
                        ->save();
                }*/

                // print_r($dataCollection);exit();
                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Variations was successfully saved"));
                Mage::getSingleton("adminhtml/session")->setPincodeData(false);

                if ($this->getRequest()->getParam("back")) {
                    $this->_redirect("*/*/edit", array("id" => $model->getId()));
                    return;
                }
                $this->_redirect("*/*/");
                return;
        } 
        catch (Exception $e) {
            Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
            Mage::getSingleton("adminhtml/session")->setPincodeData($this->getRequest()->getPost());
            $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
            return;
        }

    }
                $this->_redirect("*/*/");
        }
}