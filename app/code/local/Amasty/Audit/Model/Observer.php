<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2013 Amasty (http://www.amasty.com)
* @package Amasty_Audit
 
* Audit Log Observer
*/  
class Amasty_Audit_Model_Observer
{
    protected $_logData = array();
    
    //listen controller_action_predispatch event            
    public function saveSomeEvent($observer)   
    {
        if(Mage::app()->getStore()->isAdmin()){
            $username = Mage::getSingleton('admin/session')->getUser()? Mage::getSingleton('admin/session')->getUser()->getUsername(): '';
            $user = Mage::getModel('admin/user')->loadByUsername($username);
            if($user && Mage::helper('amaudit')->isUserInLog($user->getId())) {//settings log or not user
                $path = $observer->getEvent()->getControllerAction()->getRequest()->getOriginalPathInfo();
                $this->_saveCompilation($path, $username);
                $this->_saveCache($path, $username);
                $this->_saveIndex($path, $username);
                Mage::register('amaudit_log_path', $path, true);
            }
        }
    }
    
    //listen model_delete_after event
    public function modelDeleteAfter($observer)   
    {
        if(!Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        if(!Mage::registry('amaudit_log_duplicate_save')) {
            $this->_saveLog();
            Mage::register('amaudit_log_duplicate_save', 1);    
        }
        $this->modelSaveAfter($observer, "Delete");
    }

     //listen model_save_after event
    public function modelSaveAfter($observer, $delete = null)   
    {
        $class = get_class($observer->getObject());
	
        if(!Mage::app()->getStore()->isAdmin() || !Mage::registry('amaudit_log_id') || $class == "Amasty_Audit_Model_Log" || $class == "Amasty_Audit_Model_Log_Details" || $class == "Mage_Index_Model_Event"){
            return;
        }
        $elementId = $observer->getObject()->getEntityId();
        if(!$elementId) {
            $elementId = $observer->getObject()->getId();    
        }
        $name = $observer->getObject()->getName();
        if(!$name) {
            $name = $observer->getObject()->getTitle();    
        }
        if(!Mage::registry('amaudit_log_duplicate') || $name) {
            Mage::unregister('amaudit_log_duplicate');
            Mage::register('amaudit_log_duplicate', 1);
            try  {
                 $logModel = Mage::getModel('amaudit/log')->load(Mage::registry('amaudit_log_id'));
                 $this->_logData = $logModel->getData();
                 if($logModel){
                    if($name)        $this->_logData['info'] = $name;    
                    if($elementId)    $this->_logData['element_id'] = $elementId;    
                    if($observer->getObject()->hasDataChanges())     $this->_logData['type'] = "Edit";    
                    if($observer->getObject()->isObjectNew() || ($observer->getObject()->hasDataChanges() && !$observer->getObject()->getOrigData())) {
                        $this->_logData['type'] = "New";    
                    }
                    if($observer->getObject()->isDeleted() || $delete) {
                        $this->_logData['type'] = "Delete";    
                    }
                    if($logModel->getCategoryName() == "System Configuration") {
                        $this->_logData['type'] = "Edit";
                    }
                    $logModel->setData($this->_logData);
                    $logModel->save();
                 }
            }
            catch (Exception $e) 
            {
                Mage::logException($e);
                Mage::log($e->getMessage());
            }    
        }
        
        //save details
        try{
            $entity = Mage::getModel($class)->load($elementId);
        }
        catch (Exception $e) 
        {
            $entity = false;
            //Mage::logException($e);
            //Mage::log($e->getMessage());
        }
        $logModel = Mage::getModel('amaudit/log')->load(Mage::registry('amaudit_log_id'));
        $isNew = ($logModel && $logModel->getType() == "New")? true: false;
        if($entity){
            $newMass = $entity->getData();
            if(array_key_exists('config_id', $newMass) && array_key_exists('path', $newMass) && array_key_exists('value', $newMass)) {
                $newMass = array($newMass['path'] => $newMass['value']);    
            }
            $mass = Mage::registry('amaudit_details_before');
	     Mage::unregister('amaudit_details_before');
            $this->_saveDetails($mass, $newMass, Mage::registry('amaudit_log_id'), $isNew, $class); 
        }
        if($observer->getObject()->getOrigData()) {
	    Mage::unregister('amaudit_details_before');
            $this->_saveDetails($observer->getObject()->getOrigData(), $observer->getObject()->getData(), Mage::registry('amaudit_log_id'), $isNew, $class);          
        }
	 Mage::unregister('amaudit_details_before');
    }
    
    //listen model_save_before event
    public function modelSaveDeleteBefore($observer)   
    {
	$class = get_class($observer->getObject()); 

        if(!Mage::app()->getStore()->isAdmin() || $class == "Amasty_Audit_Model_Log" ||  $class == "Mage_Core_Model_Config_Element" || $class == "Amasty_Audit_Model_Log_Details"|| $class == "Mage_Index_Model_Event"){
            return;
        }
        if(!Mage::registry('amaudit_log_duplicate_save')) {
            $this->_saveLog();
            Mage::register('amaudit_log_duplicate_save', 1);    
        }
        
        $mass = Mage::registry('amaudit_details_before')? Mage::registry('amaudit_details_before'): array();  
        $id = $observer->getObject()->getId();
        $entity = Mage::getModel($class)->load($id);
        if($entity){
            $massNew = $entity->getData();
            foreach($massNew as $mas) {
                if(!(gettype($mas)=="string"  || gettype($mas)=="boolean" || is_array($mas))) { 
                    unset($mas);
                }
            }
            
            if(array_key_exists('config_id', $massNew) && array_key_exists('path', $massNew) && array_key_exists('value', $massNew)) {
                $mass[$massNew['path']] = $massNew['value'];     
            }
            else {
                $mass += $massNew;    
            }
            
            Mage::register('amaudit_details_before', $mass, true);   
        }
	
    }
    
    
    private function _saveLog()  
    {
	if(!Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        //save log start
        $username = Mage::getSingleton('admin/session')->getUser()? Mage::getSingleton('admin/session')->getUser()->getUsername(): '';
        $path = Mage::registry('amaudit_log_path');
        $arrPath = ($path)?explode("/", $path): array();
        if(!array_key_exists(3, $arrPath)) {
            return false;
        }
        $logModel = Mage::getModel('amaudit/log');
        $this->_logData = array();
        $this->_logData['date_time'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
        $this->_logData['username'] = $username;
        if("delete" == $arrPath[3]) {
            $this->_logData['type'] = "Delete";       
        }
        else {
            $this->_logData['type'] = $arrPath[3];     
        }
        $this->_logData['category'] = $arrPath[1]. '/' . $arrPath[2];
        $this->_logData['category_name'] = Mage::helper('amaudit')->getCatNameFromArray($this->_logData['category']);;

        if($arrPath[4] == 'store') $arrPath[4] = $arrPath[6];
        $paramName = $arrPath[4] == "key"? "underfined": $arrPath[4];
        if($paramName == 'section') {
            $paramName .= '/' . $arrPath[5];
        }
        $this->_logData['parametr_name'] = $paramName;

        $storeId = 0;
        if($keyStore = array_search("store", $arrPath)){
            $storeId = $arrPath[$keyStore + 1];
        }
        $this->_logData['store_id'] = $storeId; 
                         
        $logModel->setData($this->_logData);                     
        $logModel->save();
        Mage::register('amaudit_log_id', $logModel->getEntityId(), true);
       Mage::unregister('amaudit_details_before'); 
        //save log end
    }
        
    private function _saveDetails($massOld, $massNew, $logId, $isNew = false, $model = null)  
    {
        if($isNew) {
            $massOld = $massNew;
        }
	if(!is_array($massOld)) return;
        try {
            foreach($massOld as $key=>$value){
                if(array_key_exists($key, $massNew) && $massNew[$key] !="" && $key != 'updated_at' && $key != 'created_at' && $key != 'category_name'){
                    if(((gettype($value)=="string"  || gettype($value)=="boolean") && ($value != $massNew[$key] && !(!$value && !$massNew[$key])) || $isNew)) {
                         $datailsModel = Mage::getModel('amaudit/log_details');
                        if($datailsModel->isInCollection($logId, $key, $model)){
                            continue;
                        }
                        if(!$isNew) $datailsModel->setData('old_value', $value);         
                        $datailsModel->setData('new_value', $massNew[$key]);     
                        $datailsModel->setData('name', $key); 
                        $datailsModel->setData('log_id', $logId); 
                        $datailsModel->setData('model', $model); 
                        if($key !== "media_gallery") $datailsModel->save();  
                    }
                    else if(is_array($value) && is_array($massNew[$key])) {
                        $old = $this->implode_r(',', $value);   
                        $new = $this->implode_r(',', $massNew[$key]);
                        if($old != $new || $isNew) {
                            $datailsModel = Mage::getModel('amaudit/log_details');
                            if(!$isNew) $datailsModel->setData('old_value', $old);         
                            $datailsModel->setData('new_value', $new); 
                            $datailsModel->setData('name', $key); 
                            $datailsModel->setData('log_id', $logId); 
                            $datailsModel->setData('model', $model); 
                            $datailsModel->save();   
                        }   
                    }
                }
            }
        }
        catch (Exception $e) 
        {
            Mage::log($e->getMessage());
            Mage::logException($e);
        }
    }
    
    private function _saveCompilation($path, $username) 
    {
        if(strpos($path, "compiler/process") !== false) {
            $arrPath = explode("/", $path);
            if($keyStore = array_search("process", $arrPath)){ 
                $type = $arrPath[$keyStore + 1];
                if($type != "index"){
                    try
                    {
                         $logModel = Mage::getModel('amaudit/log');
                         $this->_logData = array();
                         $this->_logData['date_time'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
                         $this->_logData['username'] = $username;
                         $this->_logData['type'] = ucfirst($type);       
                         $this->_logData['category'] = "compiler/process";
                         $this->_logData['category_name'] = "Compilation";
                         $this->_logData['parametr_name'] = 'index';
                         $this->_logData['info'] = "Compilation";
                         $storeId = 0;
                         if($keyStore = array_search("store", $arrPath)){
                             $storeId = $arrPath[$keyStore + 1];
                         }
                         $this->_logData['store_id'] = $storeId;
                         $logModel->setData($this->_logData);
                         $logModel->save();  
                    }
                    catch (Exception $e) 
                    {
                         Mage::logException($e);
                         Mage::log($e->getMessage());
                    }    
                }
            }    
        }    
    }
    
    private function _saveCache($path, $username) 
    {
        $params = Mage::app()->getRequest()->getParams();
        if(strpos($path, "admin/cache") !== false) {
            $arrPath = explode("/", $path);
            if($keyStore = array_search("cache", $arrPath)){   
                $type = $arrPath[$keyStore + 1];
                if($type != "index"){
                    try
                    {
                         $logModel = Mage::getModel('amaudit/log');
                         $this->_logData = array();
                         $this->_logData['date_time'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
                         $this->_logData['username'] = $username;
                         $this->_logData['type'] = ucfirst($type);       
                         $this->_logData['category'] = "admin/cache";
                         $this->_logData['category_name'] = "Cache";
                         $this->_logData['parametr_name'] = 'index';
                         $this->_logData['info'] = "Cache";
                         $storeId = 0;
                         if($keyStore = array_search("store", $arrPath)){
                             $storeId = $arrPath[$keyStore + 1];
                         }
                         $this->_logData['store_id'] = $storeId;
                         
                         $logModel->setData($this->_logData);
                         $logModel->save(); 
                         if(array_key_exists('types', $params)){
                            $params = Mage::helper('amaudit')->getCacheParams($params['types']); 
                            $this->_saveDetails($params, array(), $logModel->getEntityId(), true);    
                         }
                    }
                    catch (Exception $e) 
                    {
                         Mage::logException($e);
                         Mage::log($e->getMessage());
                    }    
                }
            }    
        }    
    } 
    
    private function _saveIndex($path, $username) 
    {
        $params = Mage::app()->getRequest()->getParams();
        if(strpos($path, "admin/process") !== false) {
            $arrPath = explode("/", $path);
            if($keyStore = array_search("process", $arrPath)){   //settings log or not user
                $type = $arrPath[$keyStore + 1];
                if($type != "list"){
                    try
                    {
                         $logModel = Mage::getModel('amaudit/log');
                         $this->_logData = array();
                         $this->_logData['date_time'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
                         $this->_logData['username'] = $username;
                         $this->_logData['type'] = ucfirst($type);       
                         $this->_logData['category'] = "admin/process";
                         $this->_logData['category_name'] = "Index Management";
                         $this->_logData['parametr_name'] = 'list';
                         $this->_logData['info'] = "Index Management";
                         $storeId = 0;
                         if($keyStore = array_search("store", $arrPath)){
                             $storeId = $arrPath[$keyStore + 1];
                         }
                         $this->_logData['store_id'] = $storeId;
                         
                         $logModel->setData($this->_logData);
                         $logModel->save(); 
                         if(array_key_exists('process', $params)){
                            $params = Mage::helper('amaudit')->getIndexParams($params['process']); 
                            $this->_saveDetails($params, array(), $logModel->getEntityId(), true);    
                         }
                    }
                    catch (Exception $e) 
                    {
                         Mage::logException($e);
                         Mage::log($e->getMessage());
                    }    
                }
            }    
        }    
    }
   
   //run with cron
    public function deleteLogs($observer)  
    {
        $collection = Mage::getModel('amaudit/log')->getCollection();
        $days = Mage::getStoreConfig('amaudit/general/delete_logs_afret_days');  
        try
        {
            foreach ($collection as $item) {
                $date = strtotime($item->getDateTime());
                if(time() - $date > $days * 24 * 60 * 60) {
                    $entity = Mage::getModel('amaudit/log')->load($item->getId());
                    $entity->delete();
                }
            }
        }
        catch (Exception $e) 
        {
            Mage::logException($e);
            Mage::log($e->getMessage());
        }
    }
    
    public function implode_r($glue, $arr) {
        $ret_str = "";
        foreach($arr as $a){
            $ret_str .= (is_array($a)) ? $this->implode_r($glue, $a) : "," . $a;
        }
        return $ret_str;
    }
}