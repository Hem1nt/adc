<?php
class IWD_Opc_JsonController extends Mage_Core_Controller_Front_Action{

	const XML_PATH_DEFAULT_PAYMENT = 'opc/default/payment';

	/* @var $_order Mage_Sales_Model_Order */
	protected $_order;

	/**
	 * Get Order by quoteId
	 *
	 * @return Mage_Sales_Model_Order
	 */
	protected function _getOrder(){
		if (is_null($this->_order)) {
			$this->_order = Mage::getModel('sales/order')->load($this->getOnepage()->getQuote()->getId(), 'quote_id');
			if (!$this->_order->getId()) {
				throw new Mage_Payment_Model_Info_Exception(Mage::helper('core')->__("Can not create invoice. Order was not found."));
			}
		}
		return $this->_order;
	}

	/**
	 * Create invoice
	 *
	 * @return Mage_Sales_Model_Order_Invoice
	 */
	protected function _initInvoice()
	{
		$items = array();
		foreach ($this->_getOrder()->getAllItems() as $item) {
			$items[$item->getId()] = $item->getQtyOrdered();
		}
		/* @var $invoice Mage_Sales_Model_Service_Order */
		$invoice = Mage::getModel('sales/service_order', $this->_getOrder())->prepareInvoice($items);
		$invoice->setEmailSent(true)->register();

		Mage::register('current_invoice', $invoice);
		return $invoice;
	}



	protected function _getCart(){
		return Mage::getSingleton('checkout/cart');
	}


	protected function _getSession(){
		return Mage::getSingleton('checkout/session');
	}

	protected function _getQuote(){
		return $this->_getCart()->getQuote();
	}

	/**
	 * Get one page checkout model
	 *
	 * @return Mage_Checkout_Model_Type_Onepage
	 */
	public function getOnepage(){
		return Mage::getSingleton('checkout/type_onepage');
	}

	protected function _ajaxRedirectResponse(){
		$this->getResponse()
			->setHeader('HTTP/1.1', '403 Session Expired')
			->setHeader('Login-Required', 'true')
			->sendResponse();
		return $this;
	}

	/**
	 * Validate ajax request and redirect on failure
	 *
	 * @return bool
	 */
	protected function _expireAjax(){

		if (!$this->getRequest()->isAjax()){
			$this->_redirectUrl(Mage::getBaseUrl('link', true));
			return;
		}

		if (!$this->getOnepage()->getQuote()->hasItems() || $this->getOnepage()->getQuote()->getHasError() || $this->getOnepage()->getQuote()->getIsMultiShipping()) {
			$this->_ajaxRedirectResponse();
			return true;
		}

		$action = $this->getRequest()->getActionName();
		if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true) && !in_array($action, array('index', 'progress'))) {
				$this->_ajaxRedirectResponse();
				return true;
		}

		return false;
	}

	/**
	 * Get shipping method step html
	 *
	 * @return string
	 */
	protected function _getShippingMethodsHtml(){
		$layout = $this->getLayout();
		$update = $layout->getUpdate();
		$update->load('checkout_onepage_index');
		$layout->generateXml();
		$layout->generateBlocks();
		$shippingMethods = $layout->getBlock('checkout.onepage.shipping_method');
		$shippingMethods->setTemplate('opc/onepage/shipping_method.phtml');
		return $shippingMethods->toHtml();
	}

	/**
	 * Get payments method step html
	 *
	 * @return string
	 */
	protected function _getPaymentMethodsHtml($use_method = false, $just_save = false){

		/** UPDATE PAYMENT METHOD **/
		if($use_method && $use_method != -1)
			$apply_method = $use_method;
		else
		{
			if($use_method == -1)
				$apply_method = Mage::getStoreConfig(self::XML_PATH_DEFAULT_PAYMENT);
			else
			{
				$apply_method = Mage::helper('opc')->getSelectedPaymentMethod();
				if(empty($apply_method))
					$apply_method = Mage::getStoreConfig(self::XML_PATH_DEFAULT_PAYMENT);
			}
		}

		$_cart = $this->_getCart();
		$_quote = $_cart->getQuote();
		$_quote->getPayment()->setMethod($apply_method);
		$_quote->setTotalsCollectedFlag(false)->collectTotals();
		$_quote->save();

		if($just_save)
			return '';

		$layout = $this->getLayout();
		$update = $layout->getUpdate();
		$update->load('checkout_onepage_paymentmethod');
		$layout->generateXml();
		$layout->generateBlocks();
		$output = $layout->getOutput();
		return $output;
	}

	/**
	 * Get review step html
	 *
	 * @return string
	 */
	protected function _getReviewHtml(){

		//clear cache
		// Mage::app()->getCacheInstance()->cleanType('layout');

		$layout = $this->getLayout();
		$update = $layout->getUpdate();
		$update->load('checkout_onepage_review');
		$layout->generateXml();
		$layout->generateBlocks();
		$review = $layout->getBlock('root');
		$review->setTemplate('opc/onepage/review/info.phtml');

		//$this->_firstOrderDiscount();
		//clear cache
		// Mage::app()->getCacheInstance()->cleanType('layout');

		return $review->toHtml();
	}

	protected function _firstOrderDiscount(){
		/*---------------- start of coupon for first order referral system-----------*/
		$cartObj =  Mage::getModel('checkout/session')->getQuote();
		$diff =$cartObj->getData('subtotal')-$cartObj->getData('subtotal_with_discount');
		$coupon = Mage::getModel('checkout/session')->getQuote()->getCouponCode();
		Mage::getModel('checkout/session')->getQuote()->getCouponAmount();
		if($coupon=='Referral_Discount'){
			$couponcode = '';
			Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode($couponcode)->collectTotals()->save();
		}

		$session=Mage::getSingleton('customer/session', array('name'=>'frontend') );

		if ($session->isLoggedIn()) {
			$useremail = $session->getCustomer()->getEmail();
			// echo $useremail;exit;
			$collection = Mage::getModel('rewardpoints/referral')->getCollection()
			->addFieldToFilter('rewardpoints_referral_email',$useremail);
			$orders = Mage::getResourceModel('sales/order_collection')
			->addFieldToSelect('*')
			->addFieldToFilter('customer_email', $useremail);

			$refereddata = $collection->getSize();
			$ordersdata = $orders->getSize();
			if($refereddata >=1 && $ordersdata == 0){
                //apply coupon
				if(!$coupon){
					$couponcode = 'Referral_Discount';
					Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode($couponcode)
					->collectTotals()->save();
				}
			}
		}else{
			// echo 'not logged in user';exit();
		}

        /*---------------- end of coupon for first order refereal system-----------*/
	}


	public function emailcheckAction()
    {
        $email = $this->getRequest()->getParam('email');
        $validator = new Zend_Validate_EmailAddress();
        if(!$validator->isValid($email)) {
           $result['status'] = 0;
           $result['error_status'] = 0;
           $result['message'] = $validator->getMessages();
        }else{

             $website_id = Mage::app()->getWebsite()->getId();
             $customer_exits = $this->_customerExistsAction($email,$website_id);
             if($customer_exits){
                $result['status'] = 0;
                $result['error_status'] = 1;
                $result['message'] = 'This email id is already registred';
             }else{
                $result['status'] = 1;
             }
        }
        $this->getResponse()->setHeader('Content-type','application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

    }


	public function _customerExistsAction($email, $websiteId = null)
    {
        $customer = Mage::getModel('customer/customer');
        if ($websiteId) {
            $customer->setWebsiteId($websiteId);
        }
        $customer->loadByEmail($email);
        if ($customer->getId()) {
            return true;
        }
        return false;
    }

	private function checkNewslatter(){
		$data = $this->getRequest()->getParams();
		if (isset($data['is_subscribed']) && $data['is_subscribed']==1){
			Mage::getSingleton('core/session')->setIsSubscribed(true);
		}else{
			Mage::getSingleton('core/session')->unsIsSubscribed();
		}
	}


	public function saveBillingAction(){
		if ($this->_expireAjax()) {
			return;
		}


		if ($this->getRequest()->isPost()) {

		/*Customer Telephone S*/
			if (Mage::getSingleton('customer/session')->isLoggedIn()) {
				$customer = Mage::getSingleton('customer/session')->getCustomer();
				if (!$customer->getContactNumber()){
					$data = $this->getRequest()->getPost('billing', array());
					$customer->setData('contact_number', $data['telephone']);
		    		$customer->save();
				}
			}
		/*Customer Telephone E*/
			$data = $this->getRequest()->getPost('billing', array());
			Mage::getSingleton('core/session')->setTimeforcall($data['timetocall']);
			$timeforcall = Mage::getSingleton('core/session')->getTimeforcall();

			if (Mage::getSingleton('customer/session')->isLoggedIn()){
				$customer_data=Mage::getSingleton('customer/session')->getCustomer();
				$customer_data->setData('timetocall',$timeforcall);
				$customer_data->save();
			}

			//echo $customer_data->getData('timetocall');
			if (!Mage::getSingleton('customer/session')->isLoggedIn()){
				if (isset($data['create_account']) && $data['create_account']==1){
					$this->getOnepage()->saveCheckoutMethod(Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER);
				}else{
					$this->getOnepage()->saveCheckoutMethod(Mage_Checkout_Model_Type_Onepage::METHOD_GUEST);
					unset($data['customer_password']);
					unset($data['confirm_password']);
				}
			}else{
				$this->getOnepage()->saveCheckoutMethod(Mage_Checkout_Model_Type_Onepage::METHOD_CUSTOMER);
			}



			$this->checkNewslatter();


			$customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

			if (isset($data['email'])) {
				$data['email'] = trim($data['email']);
			}

			// get grand totals before
			$totals_before = $this->_getSession()->getQuote()->getGrandTotal();

			/// get list of available methods before billing changes
			$methods_before = Mage::helper('opc')->getAvailablePaymentMethods();
			///////

			$result = $this->getOnepage()->saveBilling($data, $customerAddressId);

			if (!isset($result['error'])) {
				/* check quote for virtual */
				if ($this->getOnepage()->getQuote()->isVirtual()) {
					$result['isVirtual'] = true;
				};

				//load shipping methods block if shipping as billing;
				$data = $this->getRequest()->getPost('billing', array());
				if (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
					$result['shipping'] = $this->_getShippingMethodsHtml();
				}

				/// get list of available methods after discount changes
				$methods_after = Mage::helper('opc')->getAvailablePaymentMethods();
				///////

				// check if need to reload payment methods
				$use_method = Mage::helper('opc')->checkUpdatedPaymentMethods($methods_before, $methods_after);

				if($use_method != -1)
				{
					if(empty($use_method))
						$use_method = -1;

					// just save new method, (because retuned html is empty)
					$result['payments'] = $this->_getPaymentMethodsHtml($use_method, true);
					// and need to send reload method request
					$result['reload_payments'] = true;
				}
				/////

				// get grand totals after
				$totals_after = $this->_getSession()->getQuote()->getGrandTotal();

				if($totals_before != $totals_after)
					$result['reload_totals'] = true;

			}else{

				$responseData['error'] = true;
				$responseData['message'] = $result['message'];
			}
			$this->getResponse()->setHeader('Content-type','application/json', true);
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}
	}


	/**
	 * Shipping save action
	 */
	public function saveShippingAction(){
		if ($this->_expireAjax()) {
            return;
        }

		//TODO create response if post not exist
		$responseData = array();

		$result = array();

		if ($this->getRequest()->isPost()) {
			// get grand totals after
			$totals_before = $this->_getSession()->getQuote()->getGrandTotal();

			$data = $this->getRequest()->getPost('shipping', array());
			$customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
			$result = $this->getOnepage()->saveShipping($data, $customerAddressId);

			if (isset($result['error'])){
				$responseData['error'] = true;
				$responseData['message'] = $result['message'];
				$responseData['messageBlock'] = 'shipping';
			}else{

				$responseData['shipping'] = $this->_getShippingMethodsHtml();

				// get grand totals after
				$totals_after = $this->_getSession()->getQuote()->getGrandTotal();

				if($totals_before != $totals_after)
					$responseData['reload_totals'] = true;
			}
		}

		$this->getResponse()->setHeader('Content-type','application/json', true);
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));

	}

	/**
	 * reload available shipping methods based on address
	 */
	public function reloadShippingsPaymentsAction(){

		if ($this->_expireAjax()) {
			return;
		}

		if ($this->getRequest()->isPost()) {

			$result = array();

			$address_type = false;
			$billing = $this->getRequest()->getPost('billing', array());
			if(!empty($billing) && is_array($billing) && isset($billing['address_id'])){
				$address_type = 'billing';
				$data = $billing;
			}
			else{
				$address_type = 'shipping';
				$data = $this->getRequest()->getPost('shipping', array());
			}

			// get grand totals after
			$totals_before = $this->_getSession()->getQuote()->getGrandTotal();

			/// get list of available methods before billing changes
			$methods_before = Mage::helper('opc')->getAvailablePaymentMethods();
			///////

			$customerAddressId = $this->getRequest()->getPost($address_type.'_address_id', false);
			$cust_addr_id = $customerAddressId;

			if($address_type == 'billing')
				$address = $this->getOnepage()->getQuote()->getBillingAddress();
			else
				$address = $this->getOnepage()->getQuote()->getShippingAddress();

			if (!empty($cust_addr_id))
			{
				$cust_addr = Mage::getModel('customer/address')->load($cust_addr_id);
				if ($cust_addr->getId())
				{
					if ($cust_addr->getCustomerId() != $this->getOnepage()->getQuote()->getCustomerId())
						$result = array('error' => 1, 'message' => Mage::helper('checkout')->__('Customer Address is not valid.'));
					else
						$address->importCustomerAddress($cust_addr);
				}
			}
			else
			{
				unset($data['address_id']);
				$address->addData($data);
			}

			if(!isset($result['error'])){
				$address->implodeStreetAddress();

				$ufs = 0;

				if($address_type == 'billing'){
					if (!$this->getOnepage()->getQuote()->isVirtual())
					{
						if(isset($data['use_for_shipping']))
							$ufs = (int) $data['use_for_shipping'];

						switch($ufs)
						{
							case 0:
								$ship = $this->getOnepage()->getQuote()->getShippingAddress();
								$ship->setSameAsBilling(0);
								break;
							case 1:
								$bill = clone $address;
								$bill->unsAddressId()->unsAddressType();
								$ship = $this->getOnepage()->getQuote()->getShippingAddress();
								$ship_method = $ship->getShippingMethod();
								$ship->addData($bill->getData());
								$ship->setSameAsBilling(1)->setShippingMethod($ship_method)->setCollectShippingRates(true);
								break;
						}
					}
				}
				else
					$address->setCollectShippingRates(true);

				$this->getOnepage()->getQuote()->collectTotals()->save();

				if ($this->getOnepage()->getQuote()->isVirtual())
					$result['isVirtual'] = true;

				if(($address_type == 'billing' && $ufs == 1) || $address_type == 'shipping')
					$result['shipping'] = $this->_getShippingMethodsHtml();

				/// get list of available methods after discount changes
				$methods_after = Mage::helper('opc')->getAvailablePaymentMethods();
				///////

				// check if need to reload payment methods
				$use_method = Mage::helper('opc')->checkUpdatedPaymentMethods($methods_before, $methods_after);

				if($use_method != -1)
				{
					if(empty($use_method))
						$use_method = -1;

					// just save new method, (because retuned html is empty)
					$result['payments'] = $this->_getPaymentMethodsHtml($use_method, true);
					// and need to send reload method request
					$result['reload_payments'] = true;
				}
				else{
					// get grand totals after
					$totals_after = $this->_getSession()->getQuote()->getGrandTotal();

					if($totals_before != $totals_after)
						$result['reload_totals'] = true;
				}
				/////

			}else{
				$result['error'] = true;
				$result['message'] = $result['message'];
			}

			$this->getResponse()->setHeader('Content-type','application/json', true);
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}
	}


	/**
	 * Shipping method save action
	 */
	public function saveShippingMethodAction(){
		if ($this->_expireAjax()) {
            return;
        }
		$responseData = array();

		if ($this->getRequest()->isPost()) {

			$this->checkNewslatter();

			$data = $this->getRequest()->getPost('shipping_method', '');
			$result = $this->getOnepage()->saveShippingMethod($data);
			/*
			 $result will have erro data if shipping method is empty
			*/
			if(!$result) {
				Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method',
											array('request'=>$this->getRequest(),
											'quote'=>$this->getOnepage()->getQuote())
									);

				$this->getOnepage()->getQuote()->collectTotals();
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

				$responseData['review'] = $this->_getReviewHtml();
				$responseData['grandTotal'] = Mage::helper('opc')->getGrandTotal();
				/*$result['update_section'] = array(
						'name' => 'payment-method',
						'html' => $this->_getPaymentMethodsHtml()
				);*/
			}
			$this->getOnepage()->getQuote()->collectTotals()->save();



			$this->getResponse()->setHeader('Content-type','application/json', true);
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
		}
	}

	public function reviewAction(){
		if ($this->_expireAjax()) {
			return;
		}
		$responseData = array();
		$responseData['review'] = $this->_getReviewHtml();
		$responseData['grandTotal'] = Mage::helper('opc')->getGrandTotal();
		$this->getResponse()->setHeader('Content-type','application/json', true);
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
	}


	public function paymentsAction(){
		if ($this->_expireAjax()) {
			return;
		}
		$responseData = array();
		$responseData['payments'] = $this->_getPaymentMethodsHtml();
		$this->getResponse()->setHeader('Content-type','application/json', true);
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
	}

	/* function to set discount for echeck payment method*/
	public function echeckpayment(){
		$quote = Mage::getSingleton('checkout/cart')->getQuote();
		
		if ($quote->isVirtual()) {
		    $quote->getBillingAddress()->setPaymentMethod('echeckpayment');
		} else {
		    $quote->getShippingAddress()->setPaymentMethod('echeckpayment');
		}
		// $quote->setTotalsCollectedFlag(false)->collectTotals();
		$quote->save();
	}

	public function echeckapipayment(){
		$quote = $this->getOnepage()->getQuote();
		$quote->getPayment()->setMethod('echeckapi');
		$quote->setTotalsCollectedFlag(false)->collectTotals();
		$quote->save();
	}

	public function savePaymentAction()
	{
		if ($this->_expireAjax()) {
            return;
        }

		try {
			/*if (!$this->getRequest()->isPost()) {
				$this->_ajaxRedirectResponse();
				return;
			}*/

			// set payment to quote
			$result = array();
			$data = $this->getRequest()->getPost('payment', array());

			// condition for echeck payment method
			if($data["method"] == "echeckpayment"){
				if(empty($data["echeck_firstname"]) || empty($data["echeck_lastname"]) || empty($data["echeck_routing_number"]) || empty($data["echeck_bank_acct_num"])){
					$this->echeckpayment();
				}
			}

			// if($data["method"] == "echeckapi"){
			// 	if(empty($data["payment[cc_owner]"]) || empty($data["payment[cc_type]"]) || empty($data["payment[cc_number]"]) || empty($data["payment[cc_exp_month]"]) || empty($data["payment[cc_exp_year]"]) || empty($data["payment[cc_cid]"])){
			// 		$this->echeckapipayment();
			// 	}
			// }
			
			$result = $this->getOnepage()->savePayment($data);

			// get section and redirect data
			$redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
			$this->getOnepage()->getQuote()->setTotalsCollectedFlag(false)->collectTotals();
			if (empty($result['error'])) {
				$this->loadLayout('checkout_onepage_review');
				$result['review'] = $this->_getReviewHtml();
				$result['grandTotal'] = Mage::helper('opc')->getGrandTotal();
				$result['Status'] = (int)1;
			}
			if ($redirectUrl) {
				$result['redirect'] = $redirectUrl;
			}
		} catch (Mage_Payment_Exception $e) {
			if ($e->getFields()) {
				$result['fields'] = $e->getFields();
			}
			$result['error'] = $e->getMessage();
		} catch (Mage_Core_Exception $e) {
			$result['error'] = $e->getMessage();
		} catch (Exception $e) {
			Mage::logException($e);
			$result['error'] = $this->__('Unable to set Payment Method.');
		}

		$this->getResponse()->setHeader('Content-type','application/json', true);
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}



	/**
	* Create order action
	*/
	public function saveOrderAction(){
        if ($this->_expireAjax()) {
            return;
        }
        $shippingCountry = $this->getOnepage()->getQuote()->getShippingAddress()->getCountry();
        
        // $billingCountry = $this->getOnepage()->getQuote()->getBillingAddress()->getCountry();
        
        $allowCountries = explode(',', (string)Mage::getStoreConfig('general/country/allow'));
        /*To allow all countries to place order S*/
        $loginAsCustomer = Mage::getModel('core/cookie')->get('login_admin');
        /*To allow all countries to place order E*/
        if(!in_array($shippingCountry,$allowCountries) && ! $loginAsCustomer){
        	$result['error'] = $this->__('We dont ship to your country.');
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
			return;
        }

        // convert guest customer to reigiter
		$customer_email = $this->getOnepage()->getQuote()->getData('customer_email');

        $orderCollection = Mage::getModel('sales/order')->getCollection();
		$orders = $orderCollection->addFieldToFilter('customer_email',$customer_email);

		// calculation for customer group
		if(count($orders->getData()) > 0) {
			$groupId = 6;
		} else {
			$groupId = 1;
		}

		// check customer is guest and make it register starts
		$checkout_method = $this->getOnepage()->getQuote()->getData('checkout_method');
		$customer_id = $this->getOnepage()->getQuote()->getData('customer_id');

		// if($customer_id == '' && $checkout_method == 'guest') {
		// 	$this->forceRegister($this->getOnepage()->getQuote(),$groupId);
		// }
		// check customer is guest and make it register ends

		$version = Mage::getVersionInfo();

		$result = array();
		try {
			if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
				$postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
				if ($diff = array_diff($requiredAgreements, $postedAgreements)) {
					$result['error'] = $this->__('Please agree to all the terms and conditions before placing the order.');
					$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
					return;
				}
			}


			$data = $this->getRequest()->getPost('payment', false);
			if ($data) {
				/** Magento CE 1.8 version**/
				if ($version['minor'] == 8){

					$data['checks'] = Mage_Payment_Model_Method_Abstract::CHECK_USE_CHECKOUT
					| Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY
					| Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY
					| Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX
					| Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;

				}
				$this->getOnepage()->getQuote()->getPayment()->importData($data);
			}

			// save comments
			if (Mage::helper('opc')->isShowComment())
			{
				$comment = $this->getRequest()->getPost('customer_comment', '');
				if(empty($comment))
					$comment  = Mage::getSingleton('core/session')->getOpcOrderComment();
				else
					Mage::getSingleton('core/session')->setOpcOrderComment($comment);
			}
			///

			$this->getOnepage()->saveOrder();

			// if($customer_id == '' && $checkout_method == 'guest') {
			// 	$this->assignOrders($customer_email);
			// }

			$this->saveDetailsAction($this->_getOrder());
			/** Magento CE 1.6 version**/
			if ($version['minor']==6){
				$storeId = Mage::app()->getStore()->getId();
				$paymentHelper = Mage::helper("payment");
				$zeroSubTotalPaymentAction = $paymentHelper->getZeroSubTotalPaymentAutomaticInvoice($storeId);
				if ($paymentHelper->isZeroSubTotal($storeId)
				&& $this->_getOrder()->getGrandTotal() == 0
				&& $zeroSubTotalPaymentAction == Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE
				&& $paymentHelper->getZeroSubTotalOrderStatus($storeId) == 'pending') {
					$invoice = $this->_initInvoice();
					$invoice->getOrder()->setIsInProcess(true);
					$transactionSave = Mage::getModel('core/resource_transaction')
					->addObject($invoice)
					->addObject($invoice->getOrder());
					$transactionSave->save();
				}
			}

			$redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();

			/* Start ADC Payment Gateways Logic */
			$baseUrl = Mage::getBaseUrl();
			$id = Mage::getSingleton('checkout/session')->getLastRealOrderId();
	        $order = Mage::getModel('sales/order')->loadByIncrementId($id);
	        $paymentMenthodUsed = Mage::getSingleton('core/session')->getPaymentMethosUsed();//exit;

	        /* start of echeck for us customers */

	        $test = Mage::getSingleton('core/session')->getEcheckhell();

	        if($test == "yes"){
	        	$baseUrl = Mage::getBaseUrl();
	        	$status_check = Mage::getSingleton('core/session')->getEcheckstatus();
	        	if($status_check == "1"){

	        		Mage::log('echeck hell'.$id, null, 'stepcheckoutlog.log');

                    $redirectUrl = $baseUrl.'checkout/onepage/failure';
                    Mage::getSingleton('core/session')->unsEcheckstatus();
                    Mage::getSingleton('core/session')->unsPaymentMethosUsed();
                    $paymentMenthodUsed = Mage::getSingleton('core/session')->setPaymentMethosUsed('echeckpayment');

	        	}else{
                    //add code Insert tarnsaction id
	        		Mage::log('echeck------------------'.$id, null, 'stepcheckoutlog.log');
	        		$transId = Mage::getSingleton('core/session')->getTransactionid();
	        		Mage::log('echeck------------------'.$transId, null, 'echek3.log');
	        		$order->setEcheckTransactionid($transId);
	        		$order->save();
	        		$sendToCrm = array() ;
	        		$sendToCrm['orderID'] = $id;
	        		$sendToCrm['echeckid'] = $transId;
	        		Mage::dispatchEvent('save_echeckid_after',$sendToCrm);

	        		Mage::getSingleton('core/session')->unsTransactionid();
                    //end code
	        		$redirectUrl = $baseUrl.'checkout/onepage/success';
	        		Mage::getSingleton('core/session')->unsPaymentMethosUsed();
	        		Mage::getSingleton('core/session')->setPaymentMethosUsed('echeckpayment');
	        		Mage::getSingleton('core/session')->setEchecksuccess("1");
	        		Mage::getSingleton('core/session')->unsEcheckstatus();
                    // $order = $this->getOrder();
	        	}
	        }

	        Mage::getSingleton('core/session')->unsEcheckhell();
	        /* end of echeck for us customers */
	        $payment_method = $order->getPayment()->getMethodInstance()->getCode();

	        $order_payment_type = $order->getOrderPaymentType();
	        $order_payment_status = $order->getOrderPaymentStatus();

	        Mage::getSingleton('core/session')->setcustomOrderid($id);//exit;

	        Mage::log('---------------------------------------', null, 'checkout_log.log');
	        Mage::log('OrderId : '.$id, null, 'checkout_log.log');
	        Mage::log('Payment Type: '.$order_payment_type, null, 'checkout_log.log');
	        Mage::log('Payment Method Used: '.$paymentMenthodUsed, null, 'checkout_log.log');
	        Mage::log('Payment Method Status: '.$order_payment_status, null, 'checkout_log.log');
	        Mage::log('---------------------------------------', null, 'checkout_log.log');


	        switch ($paymentMenthodUsed) {
	        	case 'echeck':
	        		$echeckmessage = Mage::getSingleton('core/session')->getecheckcardResponce();
	        		if($echeckmessage =='FAIL'){
	        			$message = 'Payment Placed with Echeck';
	        			// $this->flushOrderHistory($order,$message);
	        			$comments = $order->getAllStatusHistory();
	        			foreach ($comments as $comment) {
	        				$comment->delete();
	        			}

	        			$orderState = "pending_payment";
	        			$orderStatus = "transaction_declined";
	        			$order->setState($orderState, $orderStatus, "", true);
	        			$order->addStatusToHistory($order->getStatus(),$message , true);
	        			$order->save();

	        			Mage::getSingleton('core/session')->setecheckcardResponce($echeckmessageresult);
	        			Mage::getSingleton('core/session')->setPaymentMethosUsed('echeck');
	        			$redirectUrl = $baseUrl.'checkout/onepage/failure';
	        		}
	        		break;
	        	case 'anytrans':
	        		$AnytranceRedirectUrl = Mage::getSingleton('core/session')->getAnytranceRedirectUrl();
	        		if($AnytranceRedirectUrl){
	        			$message = 'Payment Placed with anyBilling';
	        			// $this->flushOrderHistory($order,$message);
	        			$comments = $order->getAllStatusHistory();
	        			foreach ($comments as $comment) {
	        				$comment->delete();
	        			}

	        			$orderState = "pending_payment";
	        			$orderStatus = "transaction_declined";
	        			$order->setState($orderState, $orderStatus, "", true);
	        			$order->addStatusToHistory($order->getStatus(),$message , true);
	        			$order->save();

	        			Mage::getSingleton('core/session')->setPaymentMethosUsed('anytrans');
	        			$redirectUrl = $baseUrl.'checkout/onepage/failure';
	        		}
	        		break;
	        	case 'gspay':
	        		$gspayRedirectUrl = Mage::getSingleton('core/session')->getGspayCCRedirectUrl();
	        		$verifyPayment  = Mage::getSingleton('core/session')->getVerifyPayment();
	        		Mage::getSingleton('core/session')->unsVerifyPayment();
	        		
	        		Mage::log('------Start-----',null,'json_gspay.log');
	        		Mage::log($id,null,'json_gspay.log');
	        		Mage::log($gspayRedirectUrl,null,'json_gspay.log');
	        		Mage::log($verifyPayment,null,'json_gspay.log');
	        		Mage::log($order_payment_status,null,'json_gspay.log');
	        		Mage::log($order->getData(),null,'json_gspay.log');
	        		Mage::log('-------End-------',null,'json_gspay.log');

	        		if($gspayRedirectUrl){
	        			$message = 'Payment Placed with GSP';
	        			// $this->flushOrderHistory($order,$message);
	        			Mage::log($order_payment_status,null,'json_gspay.log');
	        			$comments = $order->getAllStatusHistory();
	        			foreach ($comments as $comment) {
	        				$comment->delete();
	        			}

	        			$orderState = "pending_payment";
	        			$orderStatus = "transaction_declined";
	        			$order->setState($orderState, $orderStatus, "", true);
	        			$order->addStatusToHistory($order->getStatus(),$message , true);
	        			$order->save();

 						Mage::log($order->getStatus(),null,'json_gspay.log');
	        			Mage::getSingleton('core/session')->setPaymentMethosUsed('gspay');
	        			$redirectUrl = $baseUrl.'checkout/onepage/failure';
	        		}
	        		Mage::getSingleton('core/session')->unsGspayCCRedirectUrl();
	        		break;
	        	case 'esafepayment':
	        		$esafeMessage = Mage::getSingleton('core/session')->getEsafepaymentResponce();
	        		if($esafeMessage =='EXE'){
	        			Mage::log($esafeMessage,null,'jasoncontroller_esafe.log');
	        			$message = 'Payment Placed with Esafepayment';
	        			// $this->flushOrderHistory($order,$message);
	        			$comments = $order->getAllStatusHistory();
	        			foreach ($comments as $comment) {
	        				$comment->delete();
	        			}

	        			$orderState = "pending_payment";
	        			$orderStatus = "transaction_declined";
	        			$order->setState($orderState, $orderStatus, "", true);
	        			$order->addStatusToHistory($order->getStatus(),$message , true);
	        			$order->save();
	        			// Mage::log('')
	        			Mage::getSingleton('core/session')->setPaymentMethosUsed('esafepayment');
	        			$redirectUrl = $baseUrl.'checkout/onepage/failure';
	        		}	
	        	default:
	        		break;
	        }

	        // exit;
			/* End ADC Payment Gateways Logic */

		} catch (Mage_Payment_Model_Info_Exception $e) {

			$message = $e->getMessage();

			if (!empty($message)) {
				$result['error'] = $message;
			}

			$result['payment'] = $this->_getPaymentMethodsHtml();

		} catch (Mage_Core_Exception $e) {
			Mage::logException($e);

			Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());

			$result['error'] = $e->getMessage();

			$gotoSection = $this->getOnepage()->getCheckout()->getGotoSection();
			if ($gotoSection) {
				$this->getOnepage()->getCheckout()->setGotoSection(null);
			}

			$updateSection = $this->getOnepage()->getCheckout()->getUpdateSection();

			if ($updateSection) {
				$this->getOnepage()->getCheckout()->setUpdateSection(null);
			}
		} catch (Exception $e) {
			Mage::logException($e);
			Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
			$result['error'] = $this->__('There was an error processing your order. Please contact us or try again later.');

		}
			// $this->getOnepage()->getQuote()->save();

		/**
		 * when there is redirect to third party, we don't want to save order yet.
		 * we will save the order in return action.
		*/
		if (isset($redirectUrl) && !empty($redirectUrl)) {
			$result['redirect'] = $redirectUrl;
		}else{
			$this->getOnepage()->getQuote()->save();
			$result['redirect'] = Mage::getUrl('checkout/onepage/success', array('_secure'=>true)) ;
		}

		$this->getResponse()->setHeader('Content-type','application/json', true);
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}

	/** For force register **/
		public function forceRegister($quote,$groupId){

		// customer model
		$CustomerModel = Mage::getModel("customer/customer");

		$customer_firstname = $quote->getData('customer_firstname');
		$customer_middlename = $quote->getData('customer_middlename');
		$customer_lastname = $quote->getData('customer_lastname');
		$customer_email = $quote->getData('customer_email');
		$customer_dob = $quote->getData('customer_dob');
		$customer_is_guest = $quote->getData('customer_is_guest');
		$customer_gender = $quote->getData('customer_gender');

		$customer_gender = $CustomerModel
        ->getAttribute('gender')
        ->getSource()
        ->getOptionId($customer_gender);

		$websiteId = Mage::app()->getWebsite()->getId();
		$store = Mage::app()->getStore();

		// generate random password
		$password = $this->random_password(8);

		// $customer = Mage::getModel("customer/customer");
		$CustomerModel  ->setWebsiteId($websiteId)
			            ->setStore($store)
			            ->setGroupId($groupId)
			            ->setFirstname($customer_firstname)
			            ->setMiddleName($customer_middlename)
			            ->setLastname($customer_lastname)
			            ->setEmail($customer_email)
			            ->setDob($customer_dob)
			            ->setGender($customer_gender)
			            ->setPassword($password);
		try{

		    $CustomerModel->save();

		    // Send registered and new password Mail to customer
		    $senderName = Mage::getStoreConfig('custom_snippet/guest_register/sender_name');
			$senderEmail = Mage::getStoreConfig('custom_snippet/guest_register/sender_email');

			$sender = array(
			            'name' => $senderName,
			            'email' => $senderEmail
			            );

			// Set recepient information

			$recepientEmail = $customer_email;
			$recepientName = $customer_firstname .' '.$customer_lastname;

			$templateId = Mage::getStoreConfig('custom_snippet/guest_register/template_id');;
			// Get Store ID
			$storeId = Mage::app()->getStore()->getId();

			// Set variables that can be used in email template
			$vars = array('email_id'     => $customer_email,
			            'password' => $password,
			            'name' => $recepientName
			            );


			$translate  = Mage::getSingleton('core/translate');

			// Send Transactional Email
			Mage::getModel('core/email_template')
			    ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);

			$translate->setTranslateInline(true);

			// get last customer created
			$collection = $CustomerModel->getCollection()->addAttributeToSelect('*')
		        ->addAttributeToSort('entity_id','desc')->setPageSize(1);

		    $customerData = $collection->getData('entity_id');
		    foreach ($customerData as $value) {
		    	$customerId = $value['entity_id'];
		    }

			// assign billing and shipping address
	 		$billAddress = $quote->getBillingAddress();
	 		$shipAddress = $quote->getShippingAddress();

	 		$helper = Mage::helper('overrides');

	 		$billingdata = $helper->SaveAddress($billAddress,$customerId,'billing');
	 		$shippingdata = $helper->SaveAddress($shipAddress,$customerId,'shipping');

            // make customer login
            $this->makeLogin($customer_email,$password);
		}
		catch (Exception $e) {
		    Zend_Debug::dump($e->getMessage());
		}
	}

	/** Make Customer Login **/
	function makeLogin($customer_email, $password) {
	    $websiteId = Mage::app()->getWebsite()->getId();
	    $store = Mage::app()->getStore();
	    $customer = Mage::getModel("customer/customer");
	    $customer->website_id = $websiteId;
	    $customer->setStore($store);
	    try {
	        $customer->loadByEmail($customer_email);
	        $session = Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
	        $session->login($customer_email, $password);

	        // register message
	        $message = $this->__('You have been Register Successfully. Kindly check your mail for Username and Password');
			Mage::getSingleton('core/session')->addSuccess($message);
	    }catch(Exception $e){
	    	echo $e->getMessage();
	    }
	}

	/** Assign Previous orders of guest to new created customer **/
	// function assignOrders($customer_email) {
	// 	$orderCollection = Mage::getModel('sales/order')->getCollection();
	// 	$orders = $orderCollection->addFieldToFilter('customer_email',$customer_email);

	// 	$customer = Mage::getModel("customer/customer");
	// 	$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
	// 	$customer->loadByEmail($customer_email);
	// 	$customerId = $customer->getId();
	// 	$customer_firstname = $customer->getFirstName();
	// 	$customer_lastname = $customer->getLastName();

	// 	// sales order collection
	// 	$SalesCollection = Mage::getModel('sales/order');

	// 	foreach ($orders as $order) {
	// 		$increment_id = $order->getData('increment_id');
	// 		$orderbyid = $SalesCollection->loadByIncrementId($increment_id);
	// 		$orderbyid->setCustomerId($customerId);
	// 		$orderbyid->setCustomerFirstname($customer_firstname);
	// 		$orderbyid->setCustomerLastname($customer_lastname);
	// 		$orderbyid->setCustomerEmail($customer_email);
	// 		$orderbyid->save();
	// 	}
	// }

	/** Generate Random Password **/
	function random_password( $length = 8 ) {
	    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
	    $password = substr( str_shuffle( $chars ), 0, $length );
	    return $password;
	}


	public function flushOrderHistory($order,$message) {
		if($order->getId()):
			$last_order_id = $order->getId();
			$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
			$connection->beginTransaction();
			$delete = $connection->query("DELETE FROM sales_flat_order_status_history WHERE parent_id=".$last_order_id);
			$orderState = "pending_payment";
			$orderStatus = "transaction_declined";
			$order->setState($orderState, $orderStatus, "", true);
			$order->addStatusToHistory($order->getStatus(),$message , true);
			$order->save();
		endif;
	}


	/** TODO MOVE TO CUSTOMER CONTROLLER **/
	protected function _getSessionCustomer(){
		return Mage::getSingleton('customer/session');
	}

	public function saveDetailsAction($orderObj) {
		$session = Mage::getSingleton('checkout/session');
		$order = Mage::getModel('sales/order')->load($orderObj->getId());

		$order->setData('physicianname',$session->getPhysicianname())
			->setData('physiciantelephone',$session->getPhysiciantelephone())
			->setData('physicianfax',$session->getPhysicianfax())
			->setData('drug_allergies',$session->getDrugallergies())
			->setData('current_medications',$session->getCurrentmedications())
			->setData('current_treatments',$session->getCurrenttreatments())
			->setData('smoke',$session->getSmoke())
			->setData('drink',$session->getDrink())
			->setData('pregnant',$session->getPregnant())
			->setData('timetocall',$session->getTimetocall())
			->setData('callforoffers',$session->getCallforfreeval())
			->setData('order_prescription',$session->getPrescription())
			->save();

		// echo "<pre>"; print_r($order->getData());exit;

	}

	public function forgotpasswordAction(){
		$response = array();
		$email = (string) $this->getRequest()->getPost('email');

		if ($email) {
			if (!Zend_Validate::is($email, 'EmailAddress')) {
				$this->_getSessionCustomer()->setForgottenEmail($email);

				$response['error'] = 1;
				$response['message'] = $this->__('Invalid email address.');
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
				return;
			}

			/** @var $customer Mage_Customer_Model_Customer */
			$customer = Mage::getModel('customer/customer')
					->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
					->loadByEmail($email);

			if ($customer->getId()) {
				try {
					$newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
					$customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
					$customer->sendPasswordResetConfirmationEmail();
				} catch (Exception $exception) {

					$response['error'] = 1;
					$response['message'] = $exception->getMessage();
					$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));

					return;
				}
			}
			$response['message']  = Mage::helper('customer')->__('If there is an account associated with %s you will receive an email with a link to reset your password.', Mage::helper('customer')->htmlEscape($email));
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		} else {


			$response['error'] = 1;
			$response['message'] = $this->__('Please enter your email.');
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));

			return;
		}
	}

	public function commentAction(){
		if ($this->_expireAjax()) {
			return;
		}
		$comment  = $this->getRequest()->getParam('comment');
		if (!empty($comment)){
			Mage::getSingleton('core/session')->setOpcOrderComment($comment);
		}else{
			Mage::getSingleton('core/session')->unsOpcOrderComment($comment);
		}
		return;
	}

	public function zipcodeAction(){
		if ($this->_expireAjax()) {
			return;
		}

		$zipCode = $this->getRequest()->getParams();
        $helper = Mage::helper('opc');
        $responce = $helper->getValues($zipCode);
        $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($responce));

	}

	public function blackListUserAction(){

		$quoteData = Mage::getSingleton('checkout/session')->getQuote();
		$data = $quoteData->getBillingAddress()->getData();

		$blacklistPhoneno = $this->blackListPhoneNumber($data);
		$blacklistAddress =  $this->blackListAddress($data);
		$blacklistEmail =  $this->blackListEmail($data);
		if($blacklistPhoneno=="yes" || $blacklistAddress=="yes" || $blacklistEmail=="yes"){
				Mage::getSingleton('core/session')->addError('You are not allowed to purchase any Product');
			$result['status'] = "YES";
		} else {
			$result['status'] = "NO";
		}
		return print_r(json_encode($result));

	}

	/* check phone number of the black list user */
	protected function blackListPhoneNumber($data){
	    foreach (unserialize(Mage::getStoreConfig("blacklist_section/blacklist/blacklist_phonenumber")) as $mapping) {
	        if(!empty($mapping['phonenumber'])){
	        	$phone = str_replace(" ", "", $mapping['phonenumber']);
	        	$telephone = str_replace(" ", "", $data["telephone"]);
	        	if (preg_match('/'.$phone.'/',$telephone) || preg_match('/'.$telephone.'/',$phone) || $telephone == $phone){
	        		$session= Mage::getSingleton('checkout/session');
	        		$quote = $session->getQuote();
	        		$cart = Mage::getModel('checkout/cart');
	        		$cartItems = $cart->getItems();
	        		foreach ($cartItems as $item){
	        			$quote->removeItem($item->getId())->save();
	        		}
	        		return 'yes';
	        	}
	        }
	    }
	}

	/* check address of the black list user */
	protected function blackListAddress($data){
	    foreach (unserialize(Mage::getStoreConfig("blacklist_section/blacklist/blacklist_address")) as $mapping) {
	        if (!empty($mapping['address1'])){
	        	if ((strtolower($data["street"][0]) == strtolower($mapping['address1'])) && (strtolower($data["street"][1]) == strtolower($mapping['address2'])) && (strtolower($data["country_id"]) == strtolower($mapping['country'])) && (strtolower($data["city"]) == strtolower($mapping['city'])) && (strtolower($data["postcode"]) == strtolower($mapping['zipcode']))){
	        		$session= Mage::getSingleton('checkout/session');
	        		$quote = $session->getQuote();
	        		$cart = Mage::getModel('checkout/cart');
	        		$cartItems = $cart->getItems();
	        		foreach ($cartItems as $item){
	        			$quote->removeItem($item->getId())->save();
	        		}
	        		return 'yes';
	            // throw new Mage_Payment_Model_Info_Exception(Mage::helper('checkout')->__('An error occured and this is the message'));
	            // Mage::getModel('core/message_collection')->add(Mage::getSingleton('core/message')->error($message));
	        	}
	        }

	    }
	}

	/* check email id of the black list user */
	protected function blackListEmail($data){
	    $cusemail = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
	    foreach (unserialize(Mage::getStoreConfig("blacklist_section/blacklist/blacklist_email")) as $mapping) {
	        if(($mapping['email'] == $data["email"] || $mapping['email'] == $cusemail) && !empty($mapping['email'])){
	            $session= Mage::getSingleton('checkout/session');
	            $quote = $session->getQuote();
	            $cart = Mage::getModel('checkout/cart');
	            $cartItems = $cart->getItems();
	            foreach ($cartItems as $item){
	                $quote->removeItem($item->getId())->save();
	            }
	            // throw new Mage_Payment_Model_Info_Exception(Mage::helper('checkout')->__('An error occured and this is the message'));
	            // Mage::getModel('core/message_collection')->add(Mage::getSingleton('core/message')->error($message));
	            return 'yes';
	        }
	    }
	}


}
