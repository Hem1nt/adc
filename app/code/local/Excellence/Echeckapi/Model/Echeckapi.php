<?php
require('Sale.php');
class Excellence_Echeckapi_Model_Echeckapi extends Mage_Payment_Model_Method_Cc
{
	protected $_code = 'echeckapi';
	protected $_formBlockType = 'echeckapi/form_echeckapi';
	protected $_infoBlockType = 'echeckapi/info_echeckapi';
	const REQUEST_METHOD_CC     = 'CREDIT';

	const RESPONSE_CODE_APPROVED = 'APPROVED';
    const RESPONSE_CODE_DECLINED = 'DECLINED';
    const RESPONSE_CODE_ERROR    = 'ERROR';
    const RESPONSE_CODE_MISSING  = 'MISSING';
    const RESPONSE_CODE_HELD     = 4;
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

	
		public function validate()
	    {
	    
	    }
	
	/** For capture **/
	public function capture(Varien_Object $payment, $amount)
	{
		$result = array();
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
		$result = array();
		$order = $payment->getOrder();
		$orderAmount =$amount; 
		$paymentoption= $payment->getCcType();
		$cardType =$payment->getCcType();
		$billingaddress = $order->getBillingAddress();
		$countryCode = $billingaddress->getData('country_id');
		$customerReg = Mage::getSingleton('customer/session')->isLoggedIn();
		$customerData =Mage::getSingleton('customer/session')->getCustomer();
		$userEmail = $customerData->getEmail();
		$limitDate = date('31-05-2013');
    	$customerDate = date('d-m-Y', strtotime($customerData->getData('created_at')));

    	$orderCount = $this->orderCount($userEmail);

    	$customerBeforeMay = Mage::getStoreConfig('payment/pay/registration_date');
        $customerBeforeMay = date('d-m-Y', strtotime($customerBeforeMay));//(31/5/2013)
        $customerAfterBeforeOct = Mage::getStoreConfig('payment/pay/customer_created_date');
        $customerAfterBeforeOct = date('d-m-Y', strtotime($customerAfterBeforeOct));//(1/10/2015)
        
    	Mage::getSingleton('core/session')->unsPaymentMethosUsed();
		$result = $this->callGspayApi($payment, $amount, 'authorize');
    	// if(strtotime($customerDate) <= strtotime($customerBeforeMay)){
    	// 	$result = $this->callGspayApi($payment, $amount, 'authorize');
    	// }
    	// else{
    	// 	$result = $this->callNewGspayApi($payment, $amount, 'authorize');
    	// }
   //  	if($orderCount >=1 && $countryCode != 'US'){
   //  		$result = $this->callNewGspayApi($payment, $amount, 'authorize');
   //  	}else{
			// $result = $this->callGspayApi($payment, $amount, 'authorize');
   //  	}

   		if($result === false) {
			$errorCode = 'Invalid Data';
			$errorMsg = $this->_getHelper()->__('Error Processing the request');
		} else {
			Mage::log($result, null, $this->getCode().'.log');
			if($result['status'] == 1){

			$order->setData('order_payment_status',$result['responce']);
			$order->setData('order_payment_type',$result['payment_methos_used']);
			$order->save();

			switch ($result['payment_methos_used']){
				case 'gspay':
							
							if(is_array($result['transaction_id'])){
								$payment->setTransactionId('No responce');
								$payment->setIsTransactionClosed(1);
							}else{
								$payment->setTransactionId($result['transaction_id']);
								$payment->setIsTransactionClosed(1);
							}

							Mage::getSingleton('core/session')->setPaymentMethosUsed('gspay');
							$responce = $result['transaction_id'];
							Mage::log($result, null, 'gspaylog.log');	

							if(!is_array($responce)){
								$order->addStatusToHistory($order->getStatus(), 'Payment Placed with GSP Transaction ID :'.$responce, false);	
							}else{
								$responce = 'No responce from Gspay';
								$order->addStatusToHistory('transaction_declined', 'Payment Placed with GSP Transaction ID :'.$responce, false);
							}
							
						break;
				case 'echeck':
								Mage::getSingleton('core/session')->setPaymentMethosUsed('echeck');
								Mage::log($result, null, 'echecklog.log');
								$responce = Mage::getSingleton('core/session')->getecheckcardTransactionid();
								$payment_responce =  Mage::getSingleton('core/session')->getecheckcardResponce();

								$payment_responce = $result['responce'];
								if($payment_responce=='APPROVED' || $payment_responce=='APPR'){
									$payment->setTransactionAdditionalInfo(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,array('key1'=>'value1','key2'=>'value2'));
									$order->addStatusToHistory($order->getStatus(), 'Payment Placed with Echeck Transaction ID :'.$responce, false);
									$order->setEcheckTransactionid($responce);
								}
								else{
									if($payment_responce=='FAIL'){
										$responce = Mage::getSingleton('core/session')->getecheckcardTransactionid();
										$order->addStatusToHistory($order->getStatus(), 'Payment Placed with Echeck Transaction ID :'.$responce, false);
										$orderState = "transaction_declined";
										$orderStatus = "transaction_declined";
										$order->setState($orderState, $orderStatus, "", true);
										$order->setEcheckTransactionid($responce);
										$order->save();
									}else{
										$responce = 'No Responce from Echeck';	
										$echeckresponce[1] = 'FAIL';
										Mage::getSingleton('core/session')->setecheckcardResponce($echeckresponce[1]);
										$order->addStatusToHistory($order->getStatus(), 'Payment Placed with Echeck Transaction ID :'.$responce, false);
										$orderState = "transaction_declined";
										$orderStatus = "transaction_declined";
										$order->setState($orderState, $orderStatus, "", true);
										$order->setEcheckTransactionid($responce);
										$order->save();
									}
								}
					break;
				case 'anytrans':
								Mage::getSingleton('core/session')->setPaymentMethosUsed('anytrans');	
								$orderid = $order->getIncrementId();
								$AnytranceRedirectUrl = Mage::getSingleton('core/session')->getAnytranceRedirectUrl();
								Mage::log($result, null, 'anytrans.log');
								Mage::log($orderid.'--------'.$cardType.'--------'.$result['responce'].'-------'.$AnytranceRedirectUrl, null, 'anytranserror.log');
								switch (strtoupper($result['responce'])) {
									case self::RESPONSE_CODE_APPROVED:
									$payment->setStatus(self::STATUS_APPROVED);
									$order->addStatusToHistory($order->getStatus(), 'Payment Placed with anyBilling Transaction ID :'.$responce, false);
									return $this;

									case self::RESPONSE_CODE_DECLINED:
									$payment->setStatus(self::STATUS_DECLINED);
									$order->addStatusToHistory($order->getStatus(), 'Transaction Declined'.$responce, false);
									$order->addStatusToHistory($order->getStatus(), 'Payment Placed with anyBilling Transaction ID :'.$responce, false);
									return $this;
									
									case self::RESPONSE_CODE_ERROR:
									$payment->setStatus(self::STATUS_DECLINED);
									$order->addStatusToHistory($order->getStatus(), 'Transaction Declined'.$responce, false);
									$order->addStatusToHistory($order->getStatus(), 'Payment Placed with anyBilling Transaction ID :'.$responce, false);
									return $this;

									default:
									$payment->setStatus(self::STATUS_DECLINED);
									$order->addStatusToHistory($order->getStatus(), 'Transaction Declined'.$responce, false);
									$order->addStatusToHistory($order->getStatus(), 'Payment Placed with anyBilling Transaction ID :'.$responce, false);
									return $this;
								}
					break;
				default:					
					break;
			}		
				
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

	public function orderCount($email){
      $status = Mage::getStoreConfig('order_status/general/selected_status');
      $statusArray = explode(',',$status);
      $order = Mage::getModel('sales/order')->getCollection()
               ->addFieldToFilter('customer_email',$email)
               ->addFieldToFilter('status',array('in' => $statusArray));
      $count = count($order);
      return $count;        
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
	private function callAnytransApi(Varien_Object $payment, $amount,$type){

		$order = $payment->getOrder();
        $this->setStore($order->getStoreId());

        if (!$payment->getPaymentType()) {
            $payment->setPaymentType(self::REQUEST_METHOD_CC);
        }

        $billingaddress = $order->getBillingAddress();
        $shippingaddress = $order->getShippingAddress();
        $orderid = $order->getIncrementId();

        $billing_region = Mage::getModel('directory/region')->load($billingaddress->getData('region_id'));
        $shipping_region = Mage::getModel('directory/region')->load($shippingaddress->getData('region_id'));

        $billingfullname = $billingaddress->getData('firstname').' '.$billingaddress->getData('lastname');
        $shippingfullname = $shippingaddress->getData('firstname').' '.$shippingaddress->getData('lastname');

        if($payment->getCcExpMonth() < 10){
            $monthyear = substr($payment->getCcExpYear(),2).'0'.$payment->getCcExpMonth();
            

        }else{
            $monthyear = substr($payment->getCcExpYear(),2).$payment->getCcExpMonth();
        }
        $userip = $_SERVER['REMOTE_ADDR'];
        $currencyDesc = $order->getBaseCurrencyCode();
        $o_request_data = new AnytransSoap();

        $o_request_data->request->Authentication->UserName = 'ADC_API';
        $o_request_data->request->Authentication->Password = 'Wz3k7NMa8y';

        $o_request_data->request->Card->Number = $payment->getCcNumber();
        // $o_request_data->request->Card->Number = 4000000000000010;
        $o_request_data->request->Card->CVV2 = $payment->getCcCid();
        $o_request_data->request->Card->ExpiryYYMM = $monthyear;
        $o_request_data->request->Card->HolderName = $billingfullname;

        $o_request_data->request->Options->IPv4Address = $userip;
// 
        $o_request_data->request->References->Client = 'TestClientRef1';
        $o_request_data->request->References->Order = $orderid;

        $o_request_data->request->Transaction->Amount = $amount*100;
        $o_request_data->request->Transaction->Currency = $currencyDesc;

        $o_request_data->request->Billing->FullName = $billingfullname;
        $o_request_data->request->Billing->Phone = $billingaddress->getData('telephone');
        $o_request_data->request->Billing->Email = $billingaddress->getData('email');
        // $o_request_data->request->Billing->StreetNumber = $billingaddress->getData();
        $o_request_data->request->Billing->StreetName = $billingaddress->getData('street');
        // $o_request_data->request->Billing->AddressUnitNumber = $billingaddress->getData();
        $o_request_data->request->Billing->CityName = $billingaddress->getData('city');
        $o_request_data->request->Billing->TerritoryCode = $billing_region->getCode();
        $o_request_data->request->Billing->CountryCode = $billingaddress->getData('country_id');
        $o_request_data->request->Billing->PostalCode = $billingaddress->getData('postcode');
        $o_request_data->request->Billing->Fax =$billingaddress->getData('fax');

        $o_request_data->request->Customer->FullName = $billingfullname;
        $o_request_data->request->Customer->Phone = $billingaddress->getData('telephone');
        $o_request_data->request->Customer->Email = $billingaddress->getData('email');
        // $o_request_data->request->Customer->StreetNumber = $billingaddress->getData();
        $o_request_data->request->Customer->StreetName = $billingaddress->getData('street');
        // $o_request_data->request->Customer->AddressUnitNumber = $billingaddress->getData();
        $o_request_data->request->Customer->CityName = $billingaddress->getData('city');
        $o_request_data->request->Customer->TerritoryCode = $billing_region->getCode();
        $o_request_data->request->Customer->CountryCode = $billingaddress->getData('country_id');
        $o_request_data->request->Customer->PostalCode = $billingaddress->getData('postcode');
        $o_request_data->request->Customer->Fax =$billingaddress->getData('fax');

        $o_request_data->request->Shipping->FullName = $shippingfullname;
        $o_request_data->request->Shipping->Phone = $billingaddress->getData('telephone');
        $o_request_data->request->Shipping->Email =$billingaddress->getData('email');
        // $o_request_data->request->Shipping->StreetNumber = 1;
        $o_request_data->request->Shipping->StreetName = $billingaddress->getData('street');
        // $o_request_data->request->Shipping->AddressUnitNumber = 101;
        $o_request_data->request->Shipping->CityName = $billingaddress->getData('city');
        $o_request_data->request->Shipping->TerritoryCode = $shipping_region->getCode();
        $o_request_data->request->Shipping->CountryCode = $billingaddress->getData('country_id');
        $o_request_data->request->Shipping->PostalCode = $billingaddress->getData('postcode');
        $o_request_data->request->Shipping->Fax = $billingaddress->getData('fax');
        // $o_request_data->request->Shipping->BusinessName = 'Hotel de Luxe';

        // $o_client = new SoapClient( 'https://psp.stg.transactium.com/ps/PSPSoap.v1000.svc?WSDL',
        $o_client = new SoapClient( 'https://psp.transactium.com/ps/PSPSoap.v1000.svc?WSDL',
            array( 'trace' => true, 'exceptions' => true, 'connection_timeout' => 90 ));

        try {
            $result = $o_client->Sale($o_request_data);
            $array = json_decode(json_encode($result), true);
            $order_status = $array['SaleResult']['Result']['Code'];

        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
            $last_request = $o_client->__getLastRequest();
            $order_status = 'Declined';

        }

        if($order_status!='Approved'){
            $redirectUrl = Mage::getBaseUrl().'checkout/onepage/failure';
            Mage::getSingleton('core/session')->unsAnytranceRedirectUrl();
            Mage::getSingleton('core/session')->setAnytranceRedirectUrl($redirectUrl);
        }
        	Mage::getSingleton('core/session')->unsPaymentMethosUsed();
		    Mage::getSingleton('core/session')->setPaymentMethosUsed('anytrans');
        // return $order_status;
        return array('payment_methos_used'=>'anytrans','status'=>1,'responce'=>$order_status,'transaction_id' => $array_data['result']['transactionID'] , 'fraud' => rand(0,1));
	}

	private function callGspayApi(Varien_Object $payment, $amount,$type){

		Mage::log('-------------------------------',null,'gspay_payment_issue.log');

		$order = $payment->getOrder();
		
		$ordered_items = $order->getAllItems();
		foreach($ordered_items as $item){
			$product_sku = $item->getSku();
			$product_name = $item->getName();
			$product_price = $item->getPrice();
			$product_qty = $item->getQtyOrdered(); 
			$order_details[]=$product_sku.' '.$product_name.','.$product_price.',qty :'.$product_qty;
		}

		$transactionDescription = implode(' ', $order_details);

		$types = Mage::getSingleton('payment/config')->getCcTypes();
		if (isset($types[$payment->getCcType()])) {
		$type = $types[$payment->getCcType()];
		}
		
		$billingaddress = $order->getBillingAddress();
		$shippingaddress = $order->getShippingAddress();
		$totals = number_format($amount, 2, '.', '');
		$orderId = $order->getIncrementId();
		$currencyDesc = $order->getBaseCurrencyCode();
		$purchaser_billingaddress = $billingaddress->getData('street').' '.$billingaddress->getData('city').' '.$billingaddress->getData('region').' '.$billingaddress->getData('postcode').' '.$billingaddress->getData('country_id');
		$purchaser_shippingaddress = $shippingaddress->getData('street').' '.$shippingaddress->getData('city').' '.$shippingaddress->getData('region').' '.$shippingaddress->getData('postcode').' '.$shippingaddress->getData('country_id');
		$fullname_billing = $billingaddress->getData('firstname').' '.$billingaddress->getData('lastname');
		$fullname_shipping = $shippingaddress->getData('firstname').' '.$shippingaddress->getData('lastname');
		$url = $this->getConfigData('gateway_url');
		$customerBrowser = $_SERVER['HTTP_USER_AGENT'];
		$customerLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		$customerScreenResolution = "1024x768 depth=32";
		$region_id = $billingaddress->getData('region_id');
		$regionModel = Mage::getModel('directory/region')->load($region_id);
		// $regioncode = $regionModel->getData('code');
		
		$regioncode = $billingaddress->getData('region');
		$billingCountry = $billingaddress->getData('country_id');
		if($billingCountry=='US'){
			$regioncode = $regionModel->getData('code');
		}

		if($payment->getCcExpMonth() < 10){
			$expmonth = '0'.$payment->getCcExpMonth();
		}else{
			$expmonth = $payment->getCcExpMonth();
		}
		// $merchant = array(
		// 		'merchantID'=> 10605,//$this->getConfigData('api_username'),
		// 		'merchantPassword'=> 'W00D1979',//$this->getConfigData('api_password'),
		// 		'merchantSiteID'=> 64218,//$this->getConfigData('site_id'),
		// 	);

		// $order_product_details = 
		// $merchant = array(
		// 		'merchantID'=> 10605,//$this->getConfigData('api_username'),
		// 		'merchantPassword'=> 'W00D1979',//$this->getConfigData('api_password'),
		// 		'merchantSiteID'=> 64218,//$this->getConfigData('site_id'),
		// 	);
		
			$merchant = array(
					'merchantID'=> 10605,//$this->getConfigData('api_username'),
					'merchantPassword'=> 'AdcxxAD12',//$this->getConfigData('api_password'),
					'merchantSiteID'=> 64218,//$this->getConfigData('site_id'),
			);
		
		$transaction = array(
				'transactionType'=>'sale',
				'transactionAmount'=>$totals,
				'transactionOrderID'=>$order->getIncrementId(),
				'transactionAffiliateID'=>'',
				'transactionDescription'=> $transactionDescription,
			);

		$customer = array(
				'customerCardNumber'=> $payment->getCcNumber(),
				'customerCardType'=>strtoupper($type),
				'customerExpireMonth'=>$expmonth,
				'customerExpireYear'=>$payment->getCcExpYear(),
				'customerCVC2'=>$payment->getCcCid(),
				'customerCardHolder'=>$billingaddress->getData('firstname').' '.$billingaddress->getData('lastname'),
				'customerFullName'=>$fullname_billing,
				'customerPhone'=>$billingaddress->getData('telephone'),
				'customerAddress'=>$purchaser_billingaddress,
				'customerCity'=> $billingaddress->getData('city'),
				'customerStateCode'=>$regioncode,
				'customerZip'=>$billingaddress->getData('postcode'),
				'customerCountryCode'=>$billingaddress->getData('country_id'),
				'customerEmail'=>$billingaddress->getData('email'),
				'customerShippingFullName'=>$fullname_shipping,
				'customerShippingPhone'=>$shippingaddress->getData('telephone'),
				'customerShippingAddress'=>$purchaser_shippingaddress,
				'customerShippingCity'=>$shippingaddress->getData('city'),
				'customerShippingStateCode'=>$shippingaddress->getData('region'),
				'customerShippingCountryCode'=>$shippingaddress->getData('country_id'),
				'customerIP'=>$_SERVER['REMOTE_ADDR'],
				'customerBrowser'=>$customerBrowser,
				'customerLanguage'=>$customerLanguage,
				'customerScreenResolution'=>$customerScreenResolution,
			);

		$fields = array(0=>'merchant',1=>'transaction','2'=>'customer');
		$fields_string="";

		foreach($fields as $value) {				
			$child_fields_string.='<'.$value.'>';
			foreach ($$value as $key =>$childValue) {					
					$child_fields_string.= '<'.$key.'>'.$childValue.'</'.$key.'>';					
			}
			$child_fields_string.='</'.$value.'>';			
		}
		$request = '<xml><request>'.$child_fields_string.'</request></xml>';
		Mage::log('request start',null,'gspay_payment_issue.log');
		Mage::log($request,null,'gspay_payment_issue.log');
		Mage::log('request end',null,'gspay_payment_issue.log');
		$url = "https://secure.gspay.com/payment/api.php";

		$ch = curl_init();
		// curl_setopt($ch, CURLOPT_URL, $url);
		// curl_setopt($ch, CURLOPT_POSTFIELDS,"request=" . $request);
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'request='.$request);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_PORT, 443);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		// $response = curl_exec($ch);

		$data = curl_exec($ch);
		curl_close($ch);

		$array_data = json_decode(json_encode(simplexml_load_string($data)), true);
		Mage::log($order->getIncrementId(),null,'gspay_payment_issue.log');
		Mage::log('responce start',null,'gspay_payment_issue.log');
		Mage::log($array_data,null,'gspay_payment_issue.log');
		Mage::log('responce end',null,'gspay_payment_issue.log');
		if(is_array($array_data['result']['resultStatus'])){
            $api_responce = implode(',',$array_data['result']['resultStatus']);
        }else{
            $api_responce = $array_data['result']['resultStatus'];
        }

		$verifyPayment = array('order_id'=>$order->getIncrementId(),'payment_methos_used'=>'gspay','responce'=>$api_responce,'transaction_id' => $array_data['result']['transactionID']);
		
		Mage::log($verifyPayment, null, 'gspayverify.log');
		Mage::getSingleton('core/session')->unsGspayCCRedirectUrl();
		Mage::getSingleton('core/session')->unsVerifyPayment();
		Mage::getSingleton('core/session')->setVerifyPayment($verifyPayment);
		

		if($array_data['result']['resultStatus']!='approved'){
			$gspay_redirecturl = Mage::getBaseUrl().'checkout/onepage/failure';
			Mage::getSingleton('core/session')->unsGspayCCRedirectUrl();
			Mage::getSingleton('core/session')->setGspayCCRedirectUrl($gspay_redirecturl); 
			
			/* patch for start order id is not going to checkout page */
			Mage::getSingleton('core/session')->unsCustomIncrementId();
			Mage::getSingleton('core/session')->setCustomIncrementId($order->getIncrementId());
			/* patch for end order id is not going to checkout page */
	
		}
		
		Mage::getSingleton('core/session')->unsPaymentMethosUsed();
		Mage::getSingleton('core/session')->setPaymentMethosUsed('gspay');
		Mage::log('-------------------------------',null,'gspay_payment_issue.log');
		return array('order_id'=>$order->getIncrementId(),'payment_methos_used'=>'gspay','status'=>1,'responce'=>$array_data['result']['resultStatus'],'transaction_id' => $array_data['result']['transactionID'] , 'fraud' => rand(0,1));
	}

	private function callNewGspayApi(Varien_Object $payment, $amount,$type){

		Mage::log('-------------------------------',null,'gspay_payment_new_account.log');

		$order = $payment->getOrder();
		
		$ordered_items = $order->getAllItems();
		foreach($ordered_items as $item){
			$product_sku = $item->getSku();
			$product_name = $item->getName();
			$product_price = $item->getPrice();
			$product_qty = $item->getQtyOrdered(); 
			$order_details[]=$product_sku.' '.$product_name.','.$product_price.',qty :'.$product_qty;
		}

		$transactionDescription = implode(' ', $order_details);

		$types = Mage::getSingleton('payment/config')->getCcTypes();
		if (isset($types[$payment->getCcType()])) {
		$type = $types[$payment->getCcType()];
		}
		
		$billingaddress = $order->getBillingAddress();
		$shippingaddress = $order->getShippingAddress();
		$totals = number_format($amount, 2, '.', '');
		$orderId = $order->getIncrementId();
		$currencyDesc = $order->getBaseCurrencyCode();
		$purchaser_billingaddress = $billingaddress->getData('street').' '.$billingaddress->getData('city').' '.$billingaddress->getData('region').' '.$billingaddress->getData('postcode').' '.$billingaddress->getData('country_id');
		$purchaser_shippingaddress = $shippingaddress->getData('street').' '.$shippingaddress->getData('city').' '.$shippingaddress->getData('region').' '.$shippingaddress->getData('postcode').' '.$shippingaddress->getData('country_id');
		$fullname_billing = $billingaddress->getData('firstname').' '.$billingaddress->getData('lastname');
		$fullname_shipping = $shippingaddress->getData('firstname').' '.$shippingaddress->getData('lastname');
		$url = $this->getConfigData('gateway_url');
		$customerBrowser = $_SERVER['HTTP_USER_AGENT'];
		$customerLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		$customerScreenResolution = "1024x768 depth=32";
		$region_id = $billingaddress->getData('region_id');
		$regionModel = Mage::getModel('directory/region')->load($region_id);
		// $regioncode = $regionModel->getData('code');

		$regioncode = $billingaddress->getData('region');
		$billingCountry = $billingaddress->getData('country_id');
		if($billingCountry=='US'){
			$regioncode = $regionModel->getData('code');
		}

		if($payment->getCcExpMonth() < 10){
			$expmonth = '0'.$payment->getCcExpMonth();
		}else{
			$expmonth = $payment->getCcExpMonth();
		}
		
		// $merchant = array(
		// 		'merchantID'=> 10605,//$this->getConfigData('api_username'),
		// 		'merchantPassword'=> 'W00D1979',//$this->getConfigData('api_password'),
		// 		'merchantSiteID'=> 64218,//$this->getConfigData('site_id'),
		// 	);

		// $order_product_details = 
		// $merchant = array(
		// 		'merchantID'=> 10605,//$this->getConfigData('api_username'),
		// 		'merchantPassword'=> 'W00D1979',//$this->getConfigData('api_password'),
		// 		'merchantSiteID'=> 64218,//$this->getConfigData('site_id'),
		// 	);
		
			$merchant = array(
					'merchantID'=> 10606,//$this->getConfigData('api_username'),
					'merchantPassword'=> 'alldaysecond123xxx',//$this->getConfigData('api_password'),
					'merchantSiteID'=> 117801,//$this->getConfigData('site_id'),
			);
		
		$transaction = array(
				'transactionType'=>'sale',
				'transactionAmount'=>$totals,
				'transactionOrderID'=>$order->getIncrementId(),
				'transactionAffiliateID'=>'',
				'transactionDescription'=> $transactionDescription,
			);

		$customer = array(
				'customerCardNumber'=> $payment->getCcNumber(),
				'customerCardType'=>strtoupper($type),
				'customerExpireMonth'=>$expmonth,
				'customerExpireYear'=>$payment->getCcExpYear(),
				'customerCVC2'=>$payment->getCcCid(),
				'customerCardHolder'=>$billingaddress->getData('firstname').' '.$billingaddress->getData('lastname'),
				'customerFullName'=>$fullname_billing,
				'customerPhone'=>$billingaddress->getData('telephone'),
				'customerAddress'=>$purchaser_billingaddress,
				'customerCity'=> $billingaddress->getData('city'),
				'customerStateCode'=>$regioncode,
				'customerZip'=>$billingaddress->getData('postcode'),
				'customerCountryCode'=>$billingaddress->getData('country_id'),
				'customerEmail'=>$billingaddress->getData('email'),
				'customerShippingFullName'=>$fullname_shipping,
				'customerShippingPhone'=>$shippingaddress->getData('telephone'),
				'customerShippingAddress'=>$purchaser_shippingaddress,
				'customerShippingCity'=>$shippingaddress->getData('city'),
				'customerShippingStateCode'=>$shippingaddress->getData('region'),
				'customerShippingCountryCode'=>$shippingaddress->getData('country_id'),
				'customerIP'=>$_SERVER['REMOTE_ADDR'],
				'customerBrowser'=>$customerBrowser,
				'customerLanguage'=>$customerLanguage,
				'customerScreenResolution'=>$customerScreenResolution,
			);

		$fields = array(0=>'merchant',1=>'transaction','2'=>'customer');
		$fields_string="";

		foreach($fields as $value) {				
			$child_fields_string.='<'.$value.'>';
			foreach ($$value as $key =>$childValue) {					
					$child_fields_string.= '<'.$key.'>'.$childValue.'</'.$key.'>';					
			}
			$child_fields_string.='</'.$value.'>';			
		}
		$request = '<xml><request>'.$child_fields_string.'</request></xml>';
		Mage::log('request start',null,'gspay_payment_new_account.log');
		Mage::log($request,null,'gspay_payment_new_account.log');
		Mage::log('request end',null,'gspay_payment_new_account.log');
		$url = "https://secure.gspay.com/payment/api.php";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"request=" . $request);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
		$data = curl_exec($ch);
		curl_close($ch);
		$array_data = json_decode(json_encode(simplexml_load_string($data)), true);
		Mage::log($order->getIncrementId(),null,'gspay_payment_new_account.log');
		Mage::log('responce start',null,'gspay_payment_new_account.log');
		Mage::log($array_data,null,'gspay_payment_new_account.log');
		Mage::log('responce end',null,'gspay_payment_new_account.log');
		if(is_array($array_data['result']['resultStatus'])){
            $api_responce = implode(',',$array_data['result']['resultStatus']);
        }else{
            $api_responce = $array_data['result']['resultStatus'];
        }

		$verifyPayment = array('order_id'=>$order->getIncrementId(),'payment_methos_used'=>'gspay','responce'=>$api_responce,'transaction_id' => $array_data['result']['transactionID']);
		
		Mage::log($verifyPayment, null, 'gspayverify.log');
		Mage::getSingleton('core/session')->unsGspayCCRedirectUrl();
		Mage::getSingleton('core/session')->unsVerifyPayment();
		Mage::getSingleton('core/session')->setVerifyPayment($verifyPayment);
		

		if($array_data['result']['resultStatus']!='approved'){
			$gspay_redirecturl = Mage::getBaseUrl().'checkout/onepage/failure';
			Mage::getSingleton('core/session')->unsGspayCCRedirectUrl();
			Mage::getSingleton('core/session')->setGspayCCRedirectUrl($gspay_redirecturl); 
			
			/* patch for start order id is not going to checkout page */
			Mage::getSingleton('core/session')->unsCustomIncrementId();
			Mage::getSingleton('core/session')->setCustomIncrementId($order->getIncrementId());
			/* patch for end order id is not going to checkout page */
	
		}
		
		Mage::getSingleton('core/session')->unsPaymentMethosUsed();
		Mage::getSingleton('core/session')->setPaymentMethosUsed('gspay');
		Mage::log('-------------------------------',null,'gspay_payment_new_account.log');
		return array('order_id'=>$order->getIncrementId(),'payment_methos_used'=>'gspay','status'=>1,'responce'=>$array_data['result']['resultStatus'],'transaction_id' => $array_data['result']['transactionID'] , 'fraud' => rand(0,1));
	}

	private function callEcheckApi(Varien_Object $payment, $amount,$type){
		// mail('nilesh.y@iksula.com', 'echeck', 'echeck');
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
		$region_id = $billingaddress->getData('region_id');
		$regionModel = Mage::getModel('directory/region')->load($region_id);
		// $regionname = $regionModel->getData('code');
		
		$regionname = $billingaddress->getData('region');
		$billingCountry = $billingaddress->getData('country_id');
		if($billingCountry=='US'){
			$regionname = $regionModel->getData('code');
		}

		// $regionname = 1;
		$paymentType = $payment->getCcType();
		if($paymentType=='VI'){
			$securityKey = '4c61363cd88d59a5c7257390fe991de9';
		}else{
			$securityKey = 'ea51c2c7074b7d0498eab39820b80cdd';
		}
		Mage::log($securityKey.'-----------'.$payment->getCcType(), null, 'type.log');
		$purchaser_billingaddress = $billingaddress->getData('street').' '.$billingaddress->getData('city').' '.$billingaddress->getData('region').' '.$billingaddress->getData('postcode').' '.$billingaddress->getData('country_id');
		// print_r($billingaddress);
		$url = $this->getConfigData('gateway_url');
		$fields = array(
				'security_key'=> $securityKey,
				// 'security_key'=> 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
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
		// $echeckresponce[1]='APPROVED';

		$order_id = $this->getOrder()->getRealOrderId();
		Mage::getSingleton('core/session')->setecheckcardResponce($echeckresponce[1]);
		$rxcustomresponce = $order_id.'=>session email :'.$emailaddresssession.'  Email id :'.$emailaddress.' <=======> '.$result;
		Mage::log($rxcustomresponce, null, 'adccustomlog.log');
		 // exit;
		// Mage::getSingleton('core/session')->setecheckcardResponce($echeckresponce[1]);
		Mage::getSingleton('core/session')->setecheckcardTransactionid($echeckresponce[0]);
		Mage::getSingleton('core/session')->setecheckcardForResult($echeckresponce[1]);
		Mage::getSingleton('core/session')->setecheckcardOrderId($order_id);
		Mage::getSingleton('core/session')->unsPaymentMethosUsed();
		Mage::getSingleton('core/session')->setPaymentMethosUsed('echeck');
		curl_close($ch);
		// exit;

		return array('payment_methos_used'=>'echeck','status'=>1,'responce'=>$echeckresponce[1],'transaction_id' => time() , 'fraud' => rand(0,1));
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
