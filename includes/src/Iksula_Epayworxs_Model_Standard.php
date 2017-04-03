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
 * @category   Mage
 * @package    Iksula_Epayworxs
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * epayworxs Standard Model
 *
 * @category   Mage
 * @package    Iksula_Epayworxs
 * @name       Iksula_Epayworxs_Model_Standard
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Iksula_Epayworxs_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'epayworxs_standard';
    protected $_formBlockType = 'epayworxs/standard_form';

    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    protected $_order = null;


    /**
     * Get Config model
     *
     * @return object Iksula_Epayworxs_Model_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('epayworxs/config');
    }

    /**
     * Payment validation
     *
     * @param   none
     * @return  Iksula_Epayworxs_Model_Standard
     */
    public function validate()
    {
        parent::validate();
        $paymentInfo = $this->getInfoInstance();
        if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
            $currency_code = $paymentInfo->getOrder()->getBaseCurrencyCode();
        } else {
            $currency_code = $paymentInfo->getQuote()->getBaseCurrencyCode();
        }
       // if ($currency_code != $this->getConfig()->getCurrency()) {
         //   Mage::throwException(Mage::helper('epayworxs')->__('Selected currency //code ('.$currency_code.') is not compatabile with epayworxs'));
       // }
        return $this;
    }

    /**
     * Capture payment
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function capture (Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }

    /**
     *  Returns Target URL
     *
     *  @return	  string Target URL
     */
    public function getepayworxsUrl ()
    {
        //return 'https://secure.ebs.in/pg/ma/sale/pay/';
        //return 'https://www.ePayWorx.com/sys/sandbox/xsci';
        return 'https://www.epayworx.com/sys/payment/xsci';
    }

    /**
     *  Return URL for epayworxs success response
     *
     *  @return	  string URL
     */
    protected function getSuccessURL()
    {
        return Mage::getUrl('epayworxs/standard/success');
    }
	
	 protected function getStatusURL()
    {
        //return Mage::getUrl('epayworxs/standard/insstatus');
		return Mage::getUrl('ipn_sample_onlineRx.php');
    }

    /**
     *  Return URL for epayworxs notification
     *
     *  @return	  string Notification URL
     */
    protected function getNotificationURL()
    {
        return Mage::getUrl('epayworxs/standard/notify');
    }

    /**
     *  Return URL for epayworxs failure response
     *
     *  @return	  string URL
     */
    protected function getFailureURL ()
    {
        return Mage::getUrl('epayworxs/standard/failure', array('_secure' => true));
    }

    /**
     *  Form block description
     *
     *  @return	 object
     */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('epayworxs/form_standard', $name);
        $block->setMethod($this->_code);
        $block->setPayment($this->getPayment());
        return $block;
    }

    /**
     *  Return Order Place Redirect URL
     *
     *  @return	  string Order Redirect URL
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('epayworxs/standard/redirect');
    }

    /**
     *  Return Standard Checkout Form Fields for request to epayworxs
     *
     *  @return	  array Array of hidden form fields
     */
    public function getStandardCheckoutFormFields ()
    {
        $order = $this->getOrder();
        if (!($order instanceof Mage_Sales_Model_Order)) {
            Mage::throwException($this->_getHelper()->__('Cannot retrieve order object'));
        }

        $billingAddress = $order->getBillingAddress();

        $streets = $billingAddress->getStreet();
        $street = isset($streets[0]) && $streets[0] != ''
                  ? $streets[0]
                  : (isset($streets[1]) && $streets[1] != '' ? $streets[1] : '');

        if ($this->getConfig()->getDescription()) {
            $transDescription = $this->getConfig()->getDescription();
        } else {
            $transDescription = Mage::helper('epayworxs')->__('Order #%s', $order->getRealOrderId());
        }

        if ($order->getCustomerEmail()) {
            $email = $order->getCustomerEmail();
        } elseif ($billingAddress->getEmail()) {
            $email = $billingAddress->getEmail();
        } else {
            $email = '';
        }

        $fields = array(
						'VERSION'       => $this->getConfig()->getModuleVersion(),
                       	'MERCHANT_ACCOUNT' => $this->getConfig()->getAccountId(),
                        'AMOUNT' => 		number_format($order->getBaseGrandTotal(), 2, '.', ''),
                        'MEMO'         =>  $order->getRealOrderId(),
                        'PAYMENT_URL'        => $this->getSuccessURL(),
						'STATUS_URL'      	 => $this->getStatusURL(),
                        'NOPAYMENT_URL'      => $this->getFailureURL(),
                        'MCC'           => 5912,
                        //'city'             => $billingAddress->getCity(),
                        //'state'            => $billingAddress->getRegionModel()->getCode(),
                        //'zip'              => $billingAddress->getPostcode(),
                        'COUNTRY'          => $billingAddress->getCountryModel()->getIso3Code()
                       // 'phone'            => $billingAddress->getTelephone(),
                        //'email'            => $email,
                       // 'cb_url'           => $this->getNotificationURL(),
                        //'cb_type'          => 'P', // POST method used (G - GET method)
                        //'decline_url'      => $this->getFailureURL(),
                      	//'cs1'              => $order->getRealOrderId()               
						);

        if ($this->getConfig()->getDebug()) {
            $debug = Mage::getModel('epayworxs/api_debug')
                ->setRequestBody($this->getepayworxsUrl()."\n".print_r($fields,1))
                ->save();
            $fields['cs2'] = $debug->getId();
        }

        return $fields;
    }

}
