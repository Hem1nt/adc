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
abstract class Iksula_Bitcoins_Model_Abstract extends Mage_Payment_Model_Method_Abstract
{
    /**
     * unique internal payment method identifier
     */
    protected $_code = 'gspay_abstract';

    protected $_formBlockType = 'bitcoins/form';
    protected $_infoBlockType = 'bitcoins/info';

    /**
     * Availability options
     */
    protected $_isGateway              = true;
    protected $_canAuthorize           = true;
    protected $_canCapture             = true;
    protected $_canCapturePartial      = false;
    protected $_canRefund              = false;
    protected $_canVoid                = false;
    protected $_canUseInternal         = true;
    protected $_canUseCheckout         = true;
    protected $_canUseForMultishipping = false;

    protected $_paymentMethod    = 'abstract';
    protected $_defaultLocale    = 'en';
    protected $_supportedLocales = array('en');
    protected $_hidelogin        = '1';

    protected $_order;

    /**
     * Get order model
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $this->_order = $this->getInfoInstance()->getOrder();
        }
        return $this->_order;
    }

    /**
     * Return url for redirection after order placed
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('bitcoins/processing/payment');
    }

    /**
     * Capture payment through Gspay api
     *
     * @param Varien_Object $payment
     * @param decimal $amount
     * @return Gspay_Gspay_Model_Abstract
     */
    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setTransactionId($this->getTransactionId())
            ->setIsTransactionClosed(0);

        return $this;
    }

    /**
     * Camcel payment
     *
     * @param Varien_Object $payment
     * @return Gspay_Gspay_Model_Abstract
     */
    public function cancel(Varien_Object $payment)
    {
        $payment->setStatus(self::STATUS_DECLINED)
            ->setTransactionId($this->getTransactionId())
            ->setIsTransactionClosed(1);

        return $this;
    }

    /**
     * Return url of payment method
     *
     * @return string
     */
    public function getUrl()
    {
         return 'https://secure.redirect2pay.com/payment/mbookers/index.php';
    }

    /**
     * Return url of payment method
     *
     * @return string
     */
    public function getLocale()
    {
        $locale = explode('_', Mage::app()->getLocale()->getLocaleCode());
        if (is_array($locale) && !empty($locale) && in_array($locale[0], $this->_supportedLocales)) {
            return $locale[0];
        }
        return $this->getDefaultLocale();
    }

    /**
     * prepare params array to send it to gateway page via POST
     *
     * @return array
     */
    public function getFormFields()
    {
        $order_id = $this->getOrder()->getRealOrderId();
        $billing  = $this->getOrder()->getBillingAddress();
        if ($this->getOrder()->getBillingAddress()->getEmail()) {
            $email = $this->getOrder()->getBillingAddress()->getEmail();
        } else {
            $email = $this->getOrder()->getCustomerEmail();
        }

	$items = $this->getOrder()->getAllItems();
	foreach ($items as $itemId => $item)
	{
		if($item->getQtyToInvoice()) $items_list[]=sprintf("%s %s, %.02f %s, qty: %d ",$item->getSku(),$item->getName(),$item->getPrice(),$this->getOrder()->getOrderCurrencyCode(),$item->getQtyToInvoice()); 		    
	}


        $params = array(
            'merchant_fields'       => 'partner',
            'partner'               => 'magento',
            'pay_to_email'          => Mage::getStoreConfig(Iksula_Bitcoins_Helper_Data::XML_PATH_EMAIL),
            'transaction_id'        => $order_id,
            'return_url'            => Mage::getUrl('bitcoins/processing/success', array('transaction_id' => $order_id)),
            'cancel_url'            => Mage::getUrl('bitcoins/processing/cancel', array('transaction_id' => $order_id)),
            'status_url'            => Mage::getUrl('bitcoins/processing/status'),
            'language'              => $this->getLocale(),
            'amount'                => round($this->getOrder()->getGrandTotal(), 2),
            'currency'              => $this->getOrder()->getOrderCurrencyCode(),
            'recipient_description' => $this->getOrder()->getStore()->getWebsite()->getName(),
            'firstname'             => $billing->getFirstname(),
            'lastname'              => $billing->getLastname(),
            'address'               => $billing->getStreet(-1),
            'postal_code'           => $billing->getPostcode(),
            'city'                  => $billing->getCity(),
            'country'               => $billing->getCountryModel()->getIso3Code(),
            'state'               => $billing->getRegionCode(),
            'pay_from_email'        => $email,
            'phone_number'          => $billing->getTelephone(),
            'detail1_description'   => Mage::helper('bitcoins')->__('Order ID'),
            'detail1_text'          => implode("\n",$items_list),
            'payment_methods'       => $this->_paymentMethod,
            'hide_login'            => $this->_hidelogin,
            'new_window_redirect'   => '1'
        );
        Mage::log($params , null ,'bitcoingspay.log');

            // add optional day of birth
        if ($billing->getDob()) {
            $params['date_of_birth'] = Mage::app()->getLocale()->date($billing->getDob(), null, null, false)->toString('dmY');
        }

        return $params;
    }
    /**
     * Get initialized flag status
     * @return true
     */
    public function isInitializeNeeded()
    {
        return true;
    }

    /**
     * Instantiate state and set it to state onject
     * //@param
     * //@param
     */
    public function initialize($paymentAction, $stateObject)
    {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
        $stateObject->setIsNotified(false);
    }

    /**
     * Get config action to process initialization
     *
     * @return string
     */
    public function getConfigPaymentAction()
    {
        $paymentAction = $this->getConfigData('payment_action');
        return empty($paymentAction) ? true : $paymentAction;
    }
}
