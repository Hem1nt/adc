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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    BluePay
 * @package     BluePay_Echeck
 * @copyright   Copyright (c) 2010 BluePay Processing, LLC (http://www.bluepay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
class BluePay_Echeck_Model_EcheckPayment extends Mage_Paygate_Model_Authorizenet
{ 

//const CGI_URL = 'https://secure.bluepay.com/interfaces/bp10emu';
	const CGI_URL = 'https://backoffice.acceptcheckstoday.com/api/add/single/';

	protected $_formBlockType = 'echeck/form_echeck';
    protected $_infoBlockType = 'echeck/info_echeck';

    const REQUEST_METHOD_CC     = 'CREDIT';
    const REQUEST_METHOD_ECHECK = 'ACH';

    const REQUEST_TYPE_AUTH_CAPTURE = 'SALE';
    const REQUEST_TYPE_AUTH_ONLY    = 'AUTH';
    const REQUEST_TYPE_CAPTURE_ONLY = 'CAPTURE';
    const REQUEST_TYPE_CREDIT       = 'CREDIT';
    const REQUEST_TYPE_VOID         = 'VOID';
    const REQUEST_TYPE_PRIOR_AUTH_CAPTURE = 'PRIOR_AUTH_CAPTURE';

    const ECHECK_ACCT_TYPE_CHECKING = 'CHECKING';
    const ECHECK_ACCT_TYPE_BUSINESS = 'BUSINESSCHECKING';
    const ECHECK_ACCT_TYPE_SAVINGS  = 'SAVINGS';

    const ECHECK_TRANS_TYPE_CCD = 'CCD';
    const ECHECK_TRANS_TYPE_PPD = 'PPD';
    const ECHECK_TRANS_TYPE_TEL = 'TEL';
    const ECHECK_TRANS_TYPE_WEB = 'WEB';

    const RESPONSE_DELIM_CHAR = ',';

    const RESPONSE_CODE_APPROVED = 'APPROVED';
    const RESPONSE_CODE_DECLINED = 'DECLINED';
    const RESPONSE_CODE_ERROR    = 'ERROR';
    const RESPONSE_CODE_HELD     = 4;
	
	const INVOICE_ID = 0;
	const BANK_NAME = 1;
	const PAYMENT_ACCOUNT = 2;
	const AUTH_CODE = 3;
	const CARD_TYPE = 4;
	const AMOUNT = 5;
	const REBID = 6;
	const AVS = 7;
	const ORDER_ID = 8;
	const CARD_EXPIRE = 9;
	const Result = 10;
	const RRNO = 11;
	const CVV2 = 12;
	const PAYMENT_TYPE = 13;
	const MESSAGE = 14;
	
	protected $responseHeaders;

    protected $_code  = 'echeckpayment';

    /**
     * Availability options
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc = false;

    public function authorize(Varien_Object $payment, $amount)
    {
	Mage::throwException(Mage::helper('echeck')->__('Error:'));
        if ($amount <= 0) {
            Mage::throwException(Mage::helper('paygate')->__('Invalid amount for authorization.'));
        }
        $payment->setTransactionType(self::REQUEST_TYPE_AUTH_CAPTURE);
        $payment->setAmount($amount);

        $request= $this->_buildRequest($payment);
        $result = $this->_postRequest($request);

        $payment->setCcApproval($result->getAuthCode())
            ->setLastTransId($result->getRrno())
            ->setTransactionId($result->getRrno())
            ->setIsTransactionClosed(0)
            ->setCcTransId($result->getRrno())
            ->setCcAvsStatus($result->getAvs())
            ->setCcCidStatus($result->getCvv2());
        Mage::throwException(Mage::helper('paygate')->__('Error: ' . $result->getMessage()));
        switch ($result->getResult()) {
            case self::RESPONSE_CODE_APPROVED:
                $payment->setStatus(self::STATUS_APPROVED);
				Mage::throwException(Mage::helper('echeck')->__('Error: ' . $result->getMessage()));
                return $this;
            case self::RESPONSE_CODE_DECLINED:
                Mage::throwException(Mage::helper('echeck')->__('The transaction has been declined'));
			case self::RESPONSE_CODE_ERROR:
                Mage::throwException(Mage::helper('echeck')->__('Error: ' . $result->getMessage()));
			default:
                Mage::throwException(Mage::helper('echeck')->__('Error!'));
        }
    }

	public function getOrdertab()
    {
        if ($this->_order == null) {
            $session = Mage::getSingleton('checkout/session');
            $this->_order = Mage::getModel('sales/order');
            $this->_order->loadByIncrementId($session->getLastRealOrderId());
        }
	
        return $this->_order;
    }
	
    public function capture(Varien_Object $payment, $amount)
    {	

		
        $error = false;
        if ($payment->getCcTransId()) {
            $payment->setTransactionType(self::REQUEST_TYPE_AUTH_CAPTURE);
        } else {
            $payment->setTransactionType(self::REQUEST_TYPE_AUTH_CAPTURE);
        }
		
        $payment->setAmount($amount);
		
        $request= $this->_buildRequest($payment);
		$echechresponse  = $this->_echeckResponse($request);
	    //$result = $this->_postRequest($request);
		
	
        /*if ($result->getResult() == self::RESPONSE_CODE_APPROVED) {
            $payment->setStatus(self::STATUS_APPROVED);
            $payment->setLastTransId($result->getRrno())
			->setTransactionId($result->getRrno());
			return $this;
			}
       if ($result->getMessage()) {
            Mage::throwException($this->_wrapGatewayError($result->getMessage()));
        }
		Mage::throwException(Mage::helper('echeck')->__('Error in capturing the payment.'));
        if ($error !== false) {
            Mage::throwException($error);
        }*/
		
		$echechresponseData = explode(",",$echechresponse);
		
		Mage::getSingleton('core/session')->setEcheckhell("yes");
		Mage::getSingleton('core/session')->setEcheckstatus($echechresponseData);
		//print_r(Mage::getSingleton('core/session')->getEchechresponse());exit;
    }
	public function _echeckResponse(Varien_Object $request)
	{
	  $state = $request['state'];
	  $sta = $state[0].$state[1];
	 
	  //print_r($request);exit;
				
	   $xmlstr = "<?xml version='1.0' encoding='UTF-8' ?>
				<transaction>
					<auth>
						<userID>".$request['merchant']."</userID>
						<userKey>L1KMratX6/prZDexXHKLzr05nixVZh+ZAlhXcO+N/k/J</userKey>
						<version>4.0</version>
						<environment>production</environment>
					</auth>
				 
					<customer>
						<firstName>".$request['name1']."</firstName>
						<lastName>".$request['name2']."</lastName>
						<address>".$request['addr1']."</address>
						<city>".$request['city']."</city>
						<state>".$sta."</state>
						<zip>".$request['zipcode']."</zip>
						<country>".$request['country']."</country>
						<phone>".$request['phone']."</phone>
						<email>".$request['email']."</email>
						<ipaddress>8.8.4.4</ipaddress>
						<accountNumber>".$request['ach_account']."</accountNumber>
						<routingNumber>".$request['ach_routing']."</routingNumber>
						<amount>".$request['amount']."</amount>
						<cknum>123</cknum>
						<signature>".$request['signature']."</signature>
						<referenceID>9999</referenceID>
					</customer>
				 
					<options>
						<storeID>1</storeID>
						<referenceID>12345</referenceID>
						<misc1></misc1>
						<misc2></misc2>
						<misc3></misc3>
						<debug>1</debug>
					</options>
				</transaction>";
				
				// important to URLEncode the data
				$xmlstr = urlencode($xmlstr);

				// The API URL to send the transaction to
				$postURL = "https://backoffice.acceptcheckstoday.com/api/add/single";
				 
				// Initialize CURL
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $postURL);
				curl_setopt($ch, CURLOPT_POST, 1);
				 
				// http_build_query is a crucial step to build the POST data
				// xml is the variable to sent containing your data
				curl_setopt($ch, CURLOPT_POSTFIELDS, "xml=$xmlstr");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				 
				/* Uncomment these only if you know what they do, and why your doing it */
				//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				 
				if(($result = curl_exec($ch)) === false) {
					// Error handling
					//echo 'Curl error: ' . curl_error($ch);
				}
				else {
					// Successful
					$xml = simplexml_load_string($result); // Result is XML format
					//echo '<pre>';
					//print_r($xml);exit;
					/*echo '<pre>';
					echo 'transid: ' . $xml->transid;         // 16 Character Transaction ID
					echo 'status: ' . $xml->status;           // Current Transaction Status - See the API page for possible values
					echo 'misc1: ' . $xml->misc1;             // Your misc1 field - We recommend to use misc1 as a unique identifier in your system
					echo 'misc2: ' . $xml->misc2;             // Your misc2 field
					echo 'misc3: ' . $xml->misc3;             // Your misc3 field
					echo 'ret_code: ' . $xml->ret_code;      // Banking R-Code - See the API page for description
					echo 'error: ' . $xml->error;             // Error, if any - See the API page for more information
					echo 'debug: ' . $xml->debug; */            // Debug data, sends back your POST data
				}
				curl_close ($ch);
				
	     return $xml->transid.",".$xml->status.",".$xml->error;
	}
    public function void(Varien_Object $payment)
    {
       /* $error = false;
        if($payment->getParentTransactionId()){
            $payment->setTransactionType(self::REQUEST_TYPE_CREDIT);
            $request = $this->_buildRequest($payment);
            $result = $this->_postRequest($request);
            if($result->getResult()==self::RESPONSE_CODE_APPROVED){
                 $payment->setStatus(self::STATUS_SUCCESS );
				 $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
				 return $this;
            }
            $payment->setStatus(self::STATUS_ERROR);
            Mage::throwException($this->_wrapGatewayError($result->getMessage()));
		}
		$payment->setStatus(self::STATUS_ERROR);
		Mage::throwException(Mage::helper('echeck')->__('Invalid transaction ID.'));
		*/
    }

    public function refund(Varien_Object $payment, $amount)
    {
        if ($payment->getRefundTransactionId() && $amount > 0) {
            $payment->setTransactionType(self::REQUEST_TYPE_CREDIT);
			$payment->setRrno($payment->getRefundTransactionId());
			$payment->setAmount($amount);
            $request = $this->_buildRequest($payment);
            $request->setRrno($payment->getRefundTransactionId());
            $result = $this->_postRequest($request);
            if ($result->getResult()==self::RESPONSE_CODE_APPROVED) {
                $payment->setStatus(self::STATUS_SUCCESS);
                return $this;
            }
			if ($result->getResult()==self::RESPONSE_CODE_DECLINED) {
                Mage::throwException($this->_wrapGatewayError('DECLINED'));
            }
			if ($result->getResult()==self::RESPONSE_CODE_ERROR) {
                Mage::throwException($this->_wrapGatewayError('ERROR'));
            }			
            Mage::throwException($this->_wrapGatewayError($result->getRrno()));
        }
        Mage::throwException(Mage::helper('echeck')->__('Error in refunding the payment.'));
    }

    protected function _buildRequest(Varien_Object $payment)
    {
		$order = $payment->getOrder();

        $this->setStore($order->getStoreId());

        if (!$payment->getPaymentType()) {
            $payment->setPaymentType(self::REQUEST_METHOD_ECHECK);
        }
		$payment->setPaymentType(self::REQUEST_METHOD_ECHECK);
        $request = Mage::getModel('echeck/EcheckPayment_request');

        if ($order && $order->getIncrementId()) {
            $request->setInvoiceID($order->getIncrementId());
        }

        $request->setMode(($this->getConfigData('test_mode') == 'TEST') ? 'TEST' : 'LIVE');
        $request->setMerchant($this->getConfigData('login'))
            ->setTransactionType($payment->getTransactionType())
            ->setPaymentType($payment->getPaymentType())
			->setTamperProofSeal($this->calcTPS($payment));
        if($payment->getAmount()){
            $request->setAmount($payment->getAmount(),2);
        }
        switch ($payment->getTransactionType()) {
            case self::REQUEST_TYPE_CREDIT:
            case self::REQUEST_TYPE_VOID:
            case self::REQUEST_TYPE_CAPTURE_ONLY:
                $request->setRrno($payment->getCcTransId());
                break;
        }
		$cart = Mage::helper('checkout/cart')->getCart()->getItemsCount();
		$cartSummary = Mage::helper('checkout/cart')->getCart()->getSummaryQty();
		Mage::getSingleton('core/session', array('name'=>'frontend'));
		$session = Mage::getSingleton('checkout/session');

		$comment = "";

		foreach ($session->getQuote()->getAllItems() as $item) {
    
			$comment .= $item->getQty() . ' ';
			$comment .= '[' . $item->getSku() . ']' . ' ';
			$comment .= $item->getName() . ' ';
			$comment .= $item->getDescription() . ' ';
			$comment .= $item->getBaseCalculationPrice . ' ';
		}

		
        if (!empty($order)) {
            $billing = $order->getBillingAddress();
            if (!empty($billing)) {
                $request->setName1($billing->getFirstname())
                    ->setName2($billing->getLastname())
                    ->setCompany($billing->getCompany())
                    ->setAddr1($billing->getStreet(1))
                    ->setCity($billing->getCity())
                    ->setState($billing->getRegion())
                    ->setZipcode($billing->getPostcode())
                    ->setCountry($billing->getCountry())
                    ->setPhone($billing->getTelephone())
                    ->setFax($billing->getFax())
                    ->setCustomId($billing->getCustomerId())
					->setComment($comment)
                    ->setEmail($order->getCustomerEmail());
            }
        }
       
       /* switch ($payment->getPaymentType()) {
		
            //case self::REQUEST_METHOD_ECHECK:
                $request->setAchRouting($payment->getEcheckRoutingNumber())
                    ->setAchAccount($payment->getEcheckBankAcctNum())
                    ->setAchAccountType($payment->getEcheckAccountType())
                    ->setName($payment->getEcheckAccountName())
                    ->setDocType(self::ECHECK_TRANS_TYPE_CCD);
                break;
        }*/
		//new code

			$request->setAchRouting($payment->getEcheckRoutingNumber())
                    ->setAchAccount($payment->getEcheckBankAcctNum())
                    ->setAchAccountType($payment->getEcheckAccountType())
                    ->setName($payment->getEcheckAccountName())
                    ->setDocType(self::ECHECK_TRANS_TYPE_CCD);
					
		
		
			$paymentInfo = $this->getData('info_instance');
			$payment = Mage::app()->getRequest()->getParams();
			$request->setSignature($payment['payment']['signature']);
	
		//end code
            return $request;
    }

    protected function _postRequest(Varien_Object $request)
    {
	    
		$debugData = array('request' => $request->getData());
		$result = Mage::getModel('echeck/EcheckPayment_result');
		$client = new Varien_Http_Client();

        $uri = $this->getConfigData('cgi_url');
		$client->setUri(self::CGI_URL);
        $client->setConfig(array(	
            'maxredirects'=>0,
            'timeout'=>30,
        ));
        $client->setParameterPost($request->getData());
        $client->setMethod(Zend_Http_Client::POST);

		

        try {
            $response = $client->request();
        }
        catch (Exception $e) {
            $debugData['result'] = $result->getData();
            $this->_debug($debugData);
            Mage::throwException($this->_wrapGatewayError($e->getMessage()));
        }
		
		$r = $response->getHeader('location');
        if ($r) {
            $result->setResult($this->parseHeader($r, 'value', self::Result))
                ->setInvoiceId($this->parseHeader($r, 'value', self::INVOICE_ID))
                ->setMessage($this->parseHeader($r, 'value', self::MESSAGE))
                ->setAuthCode($this->parseHeader($r, 'value', self::AUTH_CODE))
                ->setAvs($this->parseHeader($r, 'value', self::AVS))
                ->setRrno($this->parseHeader($r, 'value', self::RRNO))
                ->setAmount($this->parseHeader($r, 'value', self::AMOUNT))
                ->setPaymentType($this->parseHeader($r, 'value', self::PAYMENT_TYPE))
                ->setOrderId($this->parseHeader($r, 'value', self::ORDER_ID))
                ->setCvv2($this->parseHeader($r, 'value', self::CVV2));
				if($this->parseHeader($r, 'value', 0) == "ERROR") {
					Mage::throwException($this->_wrapGatewayError($this->parseHeader($r, 'value', 1)));
				}
        } 
        else {
             Mage::throwException(
                Mage::helper('echeck')->__('Error in payment gateway.')
            );
        }

        $debugData['result'] = $result->getData();
        $this->_debug($debugData);	
        return $result;
    }
    
	public function validate()
    {
    	$paymentInfo = $this->getInfoInstance();
    	if(strlen($paymentInfo->getCcType()))
        {			
        	$paymentInfo = $this->unmapData($paymentInfo);
			
        }
    	 
         if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
             $billingCountry = $paymentInfo->getOrder()->getBillingAddress()->getCountryId();
         } else {
             $billingCountry = $paymentInfo->getQuote()->getBillingAddress()->getCountryId();
         }
         if (!$this->canUseForCountry($billingCountry)) {
             Mage::throwException($this->_getHelper()->__('Selected payment type is not allowed for billing country.'));
         }
         
        $info = $this->getInfoInstance();
        $errorMsg = false;
        $availableTypes = explode(',',$this->getConfigData('accounttypes'));

        $accountType = '';

		if (!in_array($info->getEcheckAccountType(), $availableTypes))
		{
            $errorCode = 'echeck_account_type';
            $errorMsg = $this->_getHelper()->__('Account type is not allowed for this payment method '. $info->getAccountType());
        }
        if($errorMsg)
        {
            Mage::throwException($errorMsg);
        }
        return $this;
    }
    
	public function getInfoInstance()
    {
        $instance = $this->getData('info_instance');
        return $instance;
    }
    
	public function assignData($data)
    {
	
	
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $data->setEcheckBankAcctNum4(substr($data->getEcheckBankAcctNum(), -4));
        $info = $this->getInfoInstance();
        $info->setEcheckRoutingNumber($data->getEcheckRoutingNumber())
            ->setEcheckBankName($data->getEcheckBankName())
            ->setData('echeck_account_type', $data->getEcheckAccountType())
            ->setEcheckAccountName($data->getEcheckAccountName())
            ->setEcheckBankAcctNum($data->getEcheckBankAcctNum())
            ->setEcheckBankAcctNum4($data->getEcheckBankAcctNum4())
			->setEcheckSignature($data->getSignature());
        
        $this->mapData($data);
        return $this;
    }

    public function mapData($data)
    {
	 	if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();
        $info->setCcLast4($data->getEcheckRoutingNumber())
            ->setCcNumberEnc($data->getEcheckBankName())
            ->setCcType($data->getEcheckAccountType())
            ->setCcOwner($data->getEcheckAccountName())
            ->setCcSsIssue($data->getEcheckBankAcctNum())
            ->setCcSsOwner($data->getEcheckBankAcctNum4());
			
			
    }
    
	public function unmapData($info)
    {
	  
        $info->setEcheckRoutingNumber($info->getCcLast4())
            ->setEcheckBankName($info->getCcNumberEnc())
            ->setEcheckAccountType($info->getCcType())
            ->setEcheckAccountName($info->getCcOwner())
            ->setEcheckBankAcctNum($info->getCcSsIssue())
            ->setEcheckBankAcctNum4($info->getCcSsOwner());
			
		    
	        return $info;
    }
    
    public function prepareSave()
    {
        $info = $this->getInfoInstance();
        $info->setCcSsIssue(null);
        return $this;
    }
	
	public function isAvailable($quote = null){  
		$checkResult = new StdClass;  
	$checkResult->isAvailable = (bool)(int)$this->getConfigData('active', ($quote ? $quote->getStoreId() : null));  
	Mage::dispatchEvent('payment_method_is_active', array(  
	'result' => $checkResult,  
	'method_instance' => $this,  
	'quote' => $quote,  
	));  
	return $checkResult->isAvailable;  
	} 
    
	protected final function calcTPS(Varien_Object $payment) {
	
		$order = $payment->getOrder();
		$billing = $order->getBillingAddress();

		$hashstr = $this->getConfigData('trans_key') . $this->getConfigData('login') . 
		$payment->getTransactionType() . $payment->getAmount() . $payment->getRrno() . 
		$this->getConfigData('test_mode');
	
		return bin2hex( md5($hashstr, true) );
	}	
 
	protected function parseHeader($header, $nameVal, $pos) {
		$nameVal = ($nameVal == 'name') ? '0' : '1';
		$s = explode("?", $header);
		$t = explode("&", $s[1]);
		$value = explode("=", $t[$pos]);
		return $value[$nameVal];
	}
}
