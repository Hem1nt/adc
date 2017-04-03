<?php

require_once 'Mage/Adminhtml/controllers/IndexController.php' ;

class Iksula_Orderlog_Adminhtml_IndexController extends Mage_Adminhtml_IndexController{

     public function logoutAction()   {
        // echo "vertika"; die();

        $id = Mage::getSingleton("core/session")->getUsersessionid();
        Mage::getModel("orderlog/usertimelog")->load($id)->setOutTime(now())->save();
        
        $adminSession = Mage::getSingleton('admin/session');
        $adminSession->unsetAll();
        $adminSession->getCookie()->delete($adminSession->getSessionName());
        $adminSession->addSuccess(Mage::helper('adminhtml')->__('You have logged out.'));

        $this->_redirect('*');
    }
}

?>