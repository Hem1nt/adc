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


	protected $_canSaveCc = false; //if made try, the actual credit card number and cvv code are stored in database.

	//protected $_canRefundInvoicePartial = true;
	//protected $_canVoid                 = true;
	//protected $_canUseInternal          = true;
	//protected $_canUseCheckout          = true;
	//protected $_canUseForMultishipping  = true;
	//protected $_canFetchTransactionInfo = true;
	//protected $_canReviewPayment        = true;


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
		if($result === false) {
			$errorCode = 'Invalid Data';
			$errorMsg = $this->_getHelper()->__('Error Processing the request');
		} else {
			Mage::log($result, null, $this->getCode().'.log');
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
		$result = $this->callApi($payment,$amount,'authorize');
		
		if($result === false) {
			$errorCode = 'Invalid Data';
			$errorMsg = $this->_getHelper()->__('Error Processing the request');
		} else {
			Mage::log($result, null, $this->getCode().'.log');
			if($result['status'] == 1){
				$payment->setTransactionId($result['transaction_id']);
				$payment->setIsTransactionClosed(1);				
				$payment->setTransactionAdditionalInfo(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,array('key1'=>'value1','key2'=>'value2'));
				$order->addStatusToHistory($order->getStatus(), 'Payment Sucessfully Placed with Transaction ID'.$result['transaction_id'], false);
				$order->save();
			}else{ 
				$payment->setTransactionId($result['transaction_id']);
				$payment->setIsTransactionClosed(1);
				$payment->setTransactionAdditionalInfo(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,array('key1'=>'value1','key2'=>'value2'));
				$order->addStatusToHistory($order->getStatus(), 'Payment Failed '.$result['transaction_id'], false);
				$order->save();
			}
		}
		if($errorMsg){
			echo 'sdv sfxv ';exit;
			//Mage::throwException($errorMsg);
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

		//call your authorize api here, incase of error throw exception.
		//only example code written below to show flow of code

		
		$order = $payment->getOrder();
		$types = Mage::getSingleton('payment/config')->getCcTypes();
		if (isset($types[$payment->getCcType()])) {
		$type = $types[$payment->getCcType()];
		}
		$billingaddress = $order->getBillingAddress();
		$totals = number_format($amount, 2, '.', '');
		$orderId = $order->getIncrementId();
		$currencyDesc = $order->getBaseCurrencyCode();

		$url = $this->getConfigData('gateway_url');
		$fields = array(
				'security_key'=> '4c61363cd88d59a5c7257390fe991de9',
				// 'api_username'=> $this->getConfigData('api_username'),
				// 'api_password'=> $this->getConfigData('api_password'),
				'purchaser_firstname'=> $billingaddress->getData('firstname'),
				'purchaser_lastname'=> $billingaddress->getData('lastname'),
				'purchaser_city'=> $billingaddress->getData('city'),
				'purchaser_address'=> $_SERVER["REMOTE_ADDR"],
				'purchaser_state'=> $billingaddress->getData('region'),
				'purchaser_zipcode'=> $billingaddress->getData('postcode'),
				'purchaser_country'=> $billingaddress->getData('country_id'),
				'purchaser_phone'=> $billingaddress->getData('telephone'),
				'purchaser_email'=> $billingaddress->getData('email'),
				'purchaser_dob'=> '19750729',
				'transaction_amount'=>$totals,
				'purchaser_ip'=> $_SERVER['REMOTE_ADDR'],
				'card_number'=> $payment->getCcNumber(),
				'expiration_month'=>"10",
				'expiration_year'=> "2015",
				'cvv'=> '123',
				'request_type'=> 'cc_send',
		);

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

		curl_close($ch);
		// print_r($result);
		// exit;
		$orderStatus = explode('|',$result);
		// $orderStatus[1] = 'Not Passed';
		// if($orderStatus[1]=='FAIL'){
		// 	$status = 0;
		// }
		// else{
		// 	$status = 1;
		// }
			$status = 1;
		//return array('status'=>$status,'transaction_id' => time() , 'fraud' => rand(0,1));
		return array('status'=>$status,'transaction_id' => time() , 'fraud' => rand(0,1));
	}
public function getOrderPlaceRedirectUrl()
	{
		if((int)$this->_getOrderAmount() > 0){
			return Mage::getUrl('echeckapi/index/index', array('_secure' => true));
		}else{
			return false;
		}
	}
	/*
	public function getOrderPlaceRedirectUrl()
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
