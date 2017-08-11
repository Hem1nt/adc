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

class Spadar_Login_Helper_Button extends Mage_Core_Helper_Abstract
{
    public function getButtonData()
    {   /*Set Cookie S*/
        $name= 'login_admin';
        $value= $this->getCustomerId();
        return array(
            'label'   => $this->__('Log in customer'), 
            'onclick' => 'window.open(\''.Mage::getModel('adminhtml/url')->getUrl('spadarloginadmin/', array('id' => $this->getCustomerId())).'\', \'customer\');',
            'cookie' => Mage::getModel('core/cookie')->set($name, $value), 
            );
        /*Set Cookie E*/      
        
    }

    protected function getCustomerId()
    {
        $customerId      = 0;
        $currentCustomer = Mage::registry('current_customer');
        $currentOrder    = Mage::registry('current_order');
        
        if ($currentCustomer instanceof Mage_Customer_Model_Customer)
        {
            $customerId = $currentCustomer->getId();
        }

        if ($currentOrder instanceof Mage_Sales_Model_Order)
        {
            $customerId = $currentOrder->getCustomerId();
        }

        return $customerId;
    }

    public function getButtonArea()
    {
        /* @var $adminSession Mage_Admin_Model_Session */
        $adminSession = Mage::getSingleton('admin/session');

        if (!$adminSession->isAllowed('customer/login') || !$this->getCustomerId())
        {
            return 'hidden';
        }

        return 'header';
    }
}