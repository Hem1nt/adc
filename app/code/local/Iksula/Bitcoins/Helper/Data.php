<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Gspay
 * @package     Gspay_Gspay
 * @copyright   Copyright (c) 2011 Gspay Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Iksula_Bitcoins_Helper_Data extends Mage_Payment_Helper_Data
{
    const XML_PATH_EMAIL        = 'payment/bitcoins/bitcoins_email';
    const XML_PATH_CUSTOMER_ID  = 'payment/bitcoins/customer_id';
    const XML_PATH_SECRET_KEY   = 'payment/bitcoins/secret_key';

    /**
     * Internal parameters for validation
     */
    protected $_gspayServer           = 'https://www.gspay.com';
    protected $_checkEmailUrl                = '/app/email_check.pl';
    protected $_checkEmailCustId             = '6999315';
    protected $_checkEmailPassword           = 'a4ce5a98a8950c04a3d34a2e2cb8c89f';
    protected $_checkSecretUrl               = '/app/secret_word_check.pl';
    protected $_activationEmailTo            = 'ecommerce@gspay.com';
    protected $_activationEmailSubject       = 'Magento Gspay Activation';
    protected $_gspayMasterCustId     = '7283403';
    protected $_gspayMasterSecretHash = 'c18524b6b1082653039078a4700367f0';

    /**
     * Send activation Email to Gspay
     */
    public function activateEmail()
    {
        $storeId = Mage::app()->getStore()->getId();

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        Mage::getModel('core/email_template')
            ->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
            ->sendTransactional(
                'bitcoins_activateemail',
                Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, $storeId),
                $this->_activationEmailTo,
                null,
                array(
                    'subject'     => $this->_activationEmailSubject,
                    'email_addr'  => Mage::getStoreConfig(self::XML_PATH_EMAIL),
                    'url'         => Mage::getBaseUrl(),
                    'customer_id' => Mage::getStoreConfig(self::XML_PATH_CUSTOMER_ID),
                    'language'    => Mage::getModel('core/locale')->getDefaultLocale()
                )
            );

        $translate->setTranslateInline(true);
    }

    /**
     * Check if email is registered at Gspay
     *
     * @param array $params
     * @return array
     */
    public function checkEmailRequest(Array $params) {
        $response = null;
        try {
            $response = $this->_getHttpsPage($this->_gspayServer . $this->_checkEmailUrl, array(
                'email'    => $params['email'],
                'cust_id'  => $this->_checkEmailCustId,
                'password' => $this->_checkEmailPassword)
            );
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            return null;
        }
        return $response;
    }

    /**
     * Check if entered secret is valid
     * @param array $params
     * @return array
     */
    public function checkSecretRequest(Array $params)
    {
        $response = null;
        try {
            $response = $this->_getHttpsPage($this->_gspayServer . $this->_checkSecretUrl, array(
                'email'   => $params['email'],
                'secret'  => md5(md5($params['secret']) . $this->_gspayMasterSecretHash),
                'cust_id' => $this->_gspayMasterCustId)
            );
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            return null;
        }
        return $response;
    }

    /**
     * Reading a page via HTTPS and returning its content.
     */
    protected function _getHttpsPage($host, $parameter)
    {
        
        $client = new Varien_Http_Client();
        $client->setUri($host)
            ->setConfig(array('timeout' => 30))
            ->setHeaders('accept-encoding', '')
            ->setParameterGet($parameter)
            ->setMethod(Zend_Http_Client::GET);
        $request = $client->request();
        // Workaround for pseudo chunked messages which are yet too short, so
        // only an exception is is thrown instead of returning raw body
        if (!preg_match("/^([\da-fA-F]+)[^\r\n]*\r\n/sm", $request->getRawBody(), $m))
            return $request->getRawBody();

        return $request->getBody();
    }
}
