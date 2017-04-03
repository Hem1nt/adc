<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2013 Amasty (http://www.amasty.com)
* @package Amasty_Audit
*/   
class Amasty_Audit_Helper_Data extends Mage_Core_Helper_Url
{
    public function getCatNameFromArray($name) {
        $nameArray = array(
           'amorderattr/adminhtml_order'   =>  $this->__('Amasty Order Attribute'),
	   'ampgrid/adminhtml_field'   =>  $this->__('Amasty Product Grid'),
           'admin/sales_order'                     =>  $this->__('Order'),
           'admin/sales_order_edit '                     =>  $this->__('Order'),
           'admin/catalog_product'                 =>  $this->__('Product'),
           'admin/catalog_product_attribute'       =>  $this->__('Product Attribute'),
           'admin/catalog_product_set'             =>  $this->__('Product Attribute Set'),
           'admin/tax_rule'                        =>  $this->__('Tax Rules'),
           'admin/tag'                             =>  $this->__('Tags'),
           'admin/rating'                          =>  $this->__('Rating'),
           'admin/customer_group'                  =>  $this->__('Customer Groups'),
           'admin/promo_catalog'                   =>  $this->__('Catalog Price Rules'),
           'admin/promo_quote'                     =>  $this->__('Shopping Cart Price Rules'),
           'admin/newsletter_template'             =>  $this->__('Newsletter Templates'),
           'admin/cms_page'                        =>  $this->__('CMS Manage Pages'),
           'admin/cms_block'                       =>  $this->__('CMS Static Blocks'),
           'admin/widget_instance'                 =>  $this->__('CMS Widget Instances'),
           'admin/poll'                            =>  $this->__('CMS Poll'),
           'admin/system_config'                   =>  $this->__('System Configuration'),
           'admin/permissions_user'                =>  $this->__('User'),
           'admin/permissions_role'                =>  $this->__('Role'),
           'admin/system_design'                   =>  $this->__('System Design'),
           'admin/api_user'                        =>  $this->__('System Web Services Users'),
           'admin/api_role'                        =>  $this->__('System Web Services Roles'),
           'admin/system_email_template'           =>  $this->__('System Transactional Emails'),
           'admin/system_variable'                 =>  $this->__('System Custom Variable'),
           'admin/catalog_category'                =>  $this->__('Categories'),
           'admin/urlrewrite'                      =>  $this->__('URL Rewrite Management'),
           'admin/customer'                        =>  $this->__('Customer')
       );
       
       if(array_key_exists($name, $nameArray)) {
           $name = $nameArray[$name];
       } 
       else {
           $name = ucfirst($name);
       }
         
       return $name;
    }
    
    public function getLockUser($idUser)  
    {
        try
        {
             $lockModel = Mage::getModel('amaudit/lock');
             $collection = $lockModel->getCollection();
             foreach($collection as $item){
                 if($idUser == $item->getUserId()){
                     $user = Mage::getModel('amaudit/lock')->load($item->getEntityId());
                     if($user){
                        return $user;
                     }
                     break;
                 }
             }
        }
        catch (Exception $e) 
        {
             Mage::logException($e);
        }
        
        return null;
    }
    
    public function isUserInLog($userId)
    {
        $massId = Mage::getStoreConfig('amaudit/general/log_users');
        $massId = explode(',', $massId);
        if(in_array($userId, $massId) || in_array(0, $massId)) {
            return true;    
        }
        else {
            return false;    
        }
    }
    
    public function getCacheParams($params)
    {
        $option = array();
        $cacheTypes = Mage::app()->getCacheInstance()->getTypes();
        foreach($params as $key => $value) {
            if(array_key_exists($value, $cacheTypes)){
                $option[$cacheTypes[$value]->getData('cache_type')] =  $cacheTypes[$value]->getData('description');    
            }
        }
        return $option;
    }
    
    public function getIndexParams($params)
    {
        $option = array();
        $collection = Mage::getResourceModel('index/process_collection');
        foreach($collection as $item) {
            if(in_array($item->getProcessId(), $params)){
                $option[$item->getIndexer()->getName()] =  $item->getIndexer()->getDescription();    
            }
        }
        return $option;
    }
}
