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
 
class Spadar_Login_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $hash  = $this->getRequest()->getParam('id');
        $login = Mage::getModel('spadarlogin/login')->load($hash, 'login_hash');

        $login->truncate();

        if ($login->getCustomerId())
        {
            /* @var $customerSession Mage_Customer_Model_Session */
            $customerSession = Mage::getSingleton('customer/session');
            $customerSession->loginById($login->getCustomerId());

            return $this->_redirect('customer/account/');
        }

        return $this->_redirect('');
    }
}