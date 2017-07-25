<?php
/**
 * Spadar_Login
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0), a
 * copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Spadar
 * @package    Spadar_Login
 * @author     Yury Ksenevich <sales@spadar.com>
 * @copyright  Copyright (c) 2012 Yury Ksenevich s.p.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class Spadar_Login_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        /* @var $adminSession Mage_Admin_Model_Session */
        $adminSession = Mage::getSingleton('admin/session');
        $customerId   = $this->getRequest()->getParam('id');
        $customer     = Mage::getModel('customer/customer')->load($customerId);

        if (!$adminSession->isAllowed('customer/login') || !$customer->getId()) 
        {
            return $this->_redirect('admin/');
        }

        $hash  = md5(uniqid(mt_rand(), true));
        $login = Mage::getModel('spadarlogin/login')
            ->setLoginHash($hash)
            ->setCustomerId($customerId)
            ->save();

        return $this->_redirect('spadarlogin/', array(
        	'id'     => $hash, 
        	'_store' => $customer->getStoreId(), 
            ));
    }
    protected function _isAllowed(){
        return true;
    }
}
