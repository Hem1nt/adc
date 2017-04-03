<?php
class Excellence_Echeckapi_Model_Echeckapi extends Mage_Payment_Model_Method_Cc
{
	protected $_code = 'echeckapi';
	protected $_formBlockType = 'echeckapi/form_echeckapi';
	protected $_infoBlockType = 'echeckapi/info_echeckapi';

	//protected $_isGateway               = true;
	protected $_canAuthorize            = false;
	protected $_canCapture              = false;
	//protected $_canCapturePartial       = true;
	protected $_canRefund               = false;
    protected $_canUseInternal         = false;

	protected $_canSaveCc = false; //if made try, the actual credit card number and cvv code are stored in database.

	//protected $_canRefundInvoicePartial = true;
	//protected $_canVoid                 = true;
	//protected $_canUseInternal          = true;
	//protected $_canUseCheckout          = true;
	//protected $_canUseForMultishipping  = true;
	//protected $_canFetchTransactionInfo = true;
	//protected $_canReviewPayment        = true;
	protected $_order;

	public function process($data){

		if($data['cancel'] == 1){
		 $order->getPayment()
		 ->setTransactionId(null)
		 ->setParentTransactionId(time())
		 ->void();
		 $message = 'Unable to process Payment';
		 $order->registerCancellation($message)->save();
		}
	}

	/** For capture **/
	public function capture(Varien_Object $payment, $amount)
	{
		$order = $payment->getOrder();
		$result = $this->callApi($payment,$amount,'authorize');
		// $resultemail = $result.''.
		if($result === false) {
			$errorCode = 'Invalid Data';
			$errorMsg = $this->_getHelper()->__('Error Processing the request');
		} else {
			Mage::log($result, null, $this->getCode().'.log');
			// Mage::log($resultemail, null, $this->getCode().'noresponce.log');
			//process result here to check status etc as per payment gateway.
			// if invalid status throw exception

			if($result['status'] == 1){
				$payment->setTransactionId($result['transaction_id']);
				$payment->setIsTransactionClosed(1);
				$payment->setTransactionAdditionalInfo(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,array('key1'=>'value1','key2'=>'value2'));
			}else{
				Mage::throwException($errorMsg);
			}

			// Add the comment and save the order
		}
		if($errorMsg){
			Mage::throwException($errorMsg);
		}

		return $this;
	}


	/** For authorization **/
	public function authorize(Varien_Object $payment, $amount)
	{
		$order = $payment->getOrder();
		// $result = $this->callApi($payment,$amount,'authorize');
		$paymentoption= $payment->getCcType();

		/*if($amount>=100 && $paymentoption=='VI'){
			 Mage::getSingleton('core/session')->unsLamda();
			$result = $this->callApi($payment, $amount, 'authorize');
		}
		else{
			
			$result = $this->prepareGspay($payment);
		}*/
		/*New code start*/
		 Mage::getSingleton('core/session')->unsLamda();
			$result = $this->callApi($payment, $amount, 'authorize');
		/* end */
		if($result === false) {
			$errorCode = 'Invalid Data';
			$errorMsg = $this->_getHelper()->__('Error Processing the request');
		} else {
			Mage::log($result, null, $this->getCode().'.log');
			if($result['status'] == 1){
				$payment->setTransactionId($result['transaction_id']);
				$payment->setIsTransactionClosed(1);
				$responce = Mage::getSingleton('core/session')->getecheckcardTransactionid();
				$payment_responce =  Mage::getSingleton('core/session')->getecheckcardResponce();
				if($payment_responce=='APPROVED' || $payment_responce=='APPR'){
					$payment->setTransactionAdditionalInfo(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,array('key1'=>'value1','key2'=>'value2'));
					$order->addStatusToHistory($order->getStatus(), 'Payment Placed with Transaction ID :'.$responce, false);
					$order->setEcheckTransactionid($responce);
				}
				else{
					if($payment_responce=='FAIL'){
						$responce = Mage::getSingleton('core/session')->getecheckcardTransactionid();
						$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
						$order->setEcheckTransactionid($responce);
					}else{
						$responce = 'No Responce from Echeck';
						$echeckresponce[1] = 'FAIL';
						Mage::getSingleton('core/session')->setecheckcardResponce($echeckresponce[1]);
						$order->setEcheckTransactionid($responce);
					}
				}	
				// if($payment_responce !='APPROVED'){
				// 	 $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
				// }			
				
				$order->save();
			}else{
				Mage::throwException($errorMsg);
			}

			// Add the comment and save the order
		}
		if($errorMsg){
			Mage::throwException($errorMsg);
		}

		return $this;
	}

	public function processBeforeRefund($invoice, $payment){
		return parent::processBeforeRefund($invoice, $payment);
	}
	public function refund(Varien_Object $payment, $amount){
		$order = $payment->getOrder();
		$result = $this->callApi($payment,$amount,'refund');
		if($result === false) {
			$errorCode = 'Invalid Data';
			$errorMsg = $this->_getHelper()->__('Error Processing the request');
			Mage::throwException($errorMsg);
		}
		return $this;

	}
	public function processCreditmemo($creditmemo, $payment){
		return parent::processCreditmemo($creditmemo, $payment);
	}

	private function callApi(Varien_Object $payment, $amount,$type){

	    $order = $payment->getOrder();
		$types = Mage::getSingleton('payment/config')->getCcTypes();
		if (isset($types[$payment->getCcType()])) {
		$type = $types[$payment->getCcType()];
		}
		$billingaddress = $order->getBillingAddress();
		$totals = number_format($amount, 2, '.', '');
		$orderId = $order->getIncrementId();
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
  		 /*echo $order->getCustomerEmail();
		exit;*/
		$currencyDesc = $order->getBaseCurrencyCode();
		if($payment->getCcExpMonth() < 10){
			$echeckmonth = '0'.$payment->getCcExpMonth();
		}else{
			$echeckmonth = $payment->getCcExpMonth();
		}
		$emailaddress = $billingaddress->getData('email');
		if(!$emailaddress){
			$emailaddress = Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress()->getData('email');
				if(!$emailaddress){
					$emailaddress = $order->getData('customer_email');
				}
		}
		// $emailaddresssession = Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress()->getData('email');
		// $emailaddress = $order->getBillingAddress()->getEmail();
		$region_id = $billingaddress->getData('region_id');
		$regionModel = Mage::getModel('directory/region')->load($region_id);
		$regionname = $regionModel->getData('code');
		// $regionname = 1;
		$purchaser_billingaddress = $billingaddress->getData('street').' '.$billingaddress->getData('city').' '.$billingaddress->getData('region').' '.$billingaddress->getData('postcode').' '.$billingaddress->getData('country_id');
		// print_r($billingaddress);
		$url = $this->getConfigData('gateway_url');
		$fields = array(
				'security_key'=> '4c61363cd88d59a5c7257390fe991de9',
				//'security_key'=> '3d2d0569c79bf18b397fcecce1074e0c',
				//'security_key'=> 'ea51c2c7074b7d0498eab39820b80cdd',
				// 'api_username'=> $this->getConfigData('api_username'),
				// 'api_password'=> $this->getConfigData('api_password'),
				'purchaser_firstname'=> $billingaddress->getData('firstname'),
				'purchaser_lastname'=> $billingaddress->getData('lastname'),
				'purchaser_city'=> $billingaddress->getData('city'),
				'purchaser_address'=> $purchaser_billingaddress,
				'purchaser_state'=> $regionname,
				'purchaser_zipcode'=> $billingaddress->getData('postcode'),
				'purchaser_country'=> $billingaddress->getData('country_id'),
				'purchaser_phone'=> $billingaddress->getData('telephone'),
				'purchaser_email'=> $emailaddress,
				'purchaser_dob'=> '19750729',
				'transaction_amount'=>$totals,
				'purchaser_ip'=> $_SERVER['REMOTE_ADDR'],
				'card_number'=> $payment->getCcNumber(),
				'expiration_month'=>$echeckmonth,
				'transaction_currency'=> $currencyDesc,				
				'expiration_year'=> $payment->getCcExpYear(),
				'cvv'=> $payment->getCcCid(),
				'request_type'=> 'cc_send',	
		);
// transaction_currency
			// print_r($fields);
			// exit;
		$fields_string="";
		foreach($fields as $key=>$value) {
			$fields_string .= $key.'='.$value.'&';
		}
		$fields_string = substr($fields_string,0,-1);
		//open connection
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$fields_string);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,1);
		curl_setopt($ch, CURLOPT_HEADER ,0); // DO NOT RETURN HTTP HEADERS
		curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1); // RETURN THE CONTENTS OF THE CALL
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // Timeout on connect (2 minutes)
		$result = curl_exec($ch);

		$echeckresponce = explode("|",$result);
		// $echeckresponce[1]='PASS';
		// echo $echeckresponce[1];
		// exit;
			// print_r($result);

		$order_id = $this->getOrder()->getRealOrderId();
		$rxcustomresponce = $order_id.'=>session email :'.$emailaddresssession.'  Email id :'.$emailaddress.' <=======> '.$result;
		Mage::log($rxcustomresponce, null, adccustomlog.log);
		 // exit;
		Mage::getSingleton('core/session')->setecheckcardResponce($echeckresponce[1]);
		Mage::getSingleton('core/session')->setecheckcardTransactionid($echeckresponce[0]);
		Mage::getSingleton('core/session')->setecheckcardForResult($echeckresponce[1]);
		Mage::getSingleton('core/session')->setecheckcardOrderId($order_id);
		curl_close($ch);
		// exit;

		return array('status'=>1,'transaction_id' => time() , 'fraud' => rand(0,1));
	//return array('status'=>rand(0, 1),'transaction_id' => time() , 'fraud' => rand(0,1));
	}
	private function prepareGspay(Varien_Object $payment) {
		if($payment->getCcExpMonth() < 10){
			$lamdaMonth = '0'.$payment->getCcExpMonth();
		}else{
			$lamdaMonth = $payment->getCcExpMonth();
		}
		$lamdaYear = substr($payment->getCcExpYear(), 2, 2);

		$paymentInfo = array(
			'card_name'=>$payment->getCcOwner(),
			'card_number'=>$payment->getCcNumber(),
			'expire_month'=>$lamdaMonth,
			'expire_year'=>$lamdaYear,
			'card_type'=>$payment->getCcType(),
			'card_cid'=>$payment->getCcCid()
		);
		Mage::getSingleton('core/session')->setLamda(implode("@", $paymentInfo));
		return array('status'=>1,'transaction_id' => time() , 'fraud' => rand(0,1));
	}

	public function getUrl()
    {
         return 'https://secure.redirect2pay.com/payment/mbookers/index.php';
    }
    
    public function getOrder()
    {
        if (!$this->_order) {
            $this->_order = $this->getInfoInstance()->getOrder();
        }
        return $this->_order;
    }

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
            'pay_to_email'          => Mage::getStoreConfig(Gspay_Gspay_Helper_Data::XML_PATH_EMAIL),
            'transaction_id'        => $order_id,
            'return_url'            => Mage::getUrl('gspay/processing/success', array('transaction_id' => $order_id)),
            'cancel_url'            => Mage::getUrl('gspay/processing/cancel', array('transaction_id' => $order_id)),
            'status_url'            => Mage::getUrl('gspay/processing/status'),
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
            'detail1_description'   => Mage::helper('gspay')->__('Order ID'),
            'detail1_text'          => implode("\n",$items_list),
            'payment_methods'       => $this->_paymentMethod,
            'hide_login'            => $this->_hidelogin,
            'new_window_redirect'   => '1'
        );

            // add optional day of birth
        if ($billing->getDob()) {
            $params['date_of_birth'] = Mage::app()->getLocale()->date($billing->getDob(), null, null, false)->toString('dmY');
        }

        return $params;
    }

	/*public function getOrderPlaceRedirectUrl()
	{
		if((int)$this->_getOrderAmount() > 0){
			return Mage::getUrl('pay/index/index', array('_secure' => true));
		}else{
			return false;
		}
	}
	private function _getOrderAmount()
	{
		$info = $this->getInfoInstance();
		if ($this->_isPlacedOrder()) {
			return (double)$info->getOrder()->getQuoteBaseGrandTotal();
		} else {
			return (double)$info->getQuote()->getBaseGrandTotal();
		}
	}
	private function _isPlacedOrder()
	{
		$info = $this->getInfoInstance();
		if ($info instanceof Mage_Sales_Model_Quote_Payment) {
			return false;
		} elseif ($info instanceof Mage_Sales_Model_Order_Payment) {
			return true;
		}
	}
	*/
}
?>
