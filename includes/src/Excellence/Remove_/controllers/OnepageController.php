<?php
require_once 'Mage/Checkout/controllers/OnepageController.php';
class Excellence_Remove_OnepageController extends Mage_Checkout_OnepageController
{	
	
	private $_shippingCode = 'royalmail'; 
	private $_country = 'GB'; 
	
	public function getcartTotal()
	{
		return Mage::getSingleton('checkout/cart')->getQuote();
	
	}
	public function _getCheckoutSession(){
		return Mage::getSingleton('checkout/session');
	}
	public function addShipping($params = null) {
		if (Mage::registry('checkout_addShipping')) { 
			Mage::unregister('checkout_addShipping'); return; 
		}
		
		Mage::register('checkout_addShipping',true);   
		$cart = Mage::getSingleton('checkout/cart'); 
		$quote = $cart->getQuote();   
		
		/*if ($quote->getCouponCode() != '') { 
			$c = Mage::getResourceModel('salesrule/rule_collection'); 
			$c->getSelect()->where("code=?", $quote->getCouponCode()); 
			foreach ($c->getItems() as $item) { 
				$coupon = $item; 
			}   
			
			if ($coupon->getSimpleFreeShipping() > 0) { 
				$quote->getShippingAddress()->setShippingMethod($this->_shippingCode)->save(); return true; 
			} 
		
		}  */ 
			
		try { 
			$method = $quote->getShippingAddress()->getShippingMethod(); 
			
			//if ($method) return; 
			// don't overwrite what's already there if we have one set already   
			
			if ($quote->getShippingAddress()->getCountryId() == '') { 
				$quote->getShippingAddress()->setCountryId($this->_country); 
			}   
			$quote->getShippingAddress()->setCollectShippingRates(true); 
			$quote->getShippingAddress()->collectShippingRates();   
			$rates = $quote->getShippingAddress()->getAllShippingRates(); 
			$allowed_rates = array(); 
			
			foreach ($rates as $rate) { 
				array_push($allowed_rates,$rate->getCode()); 
			}   
			
			if (!in_array($this->_shippingCode,$allowed_rates) && count($allowed_rates) > 0) { 
				$shippingCode = $allowed_rates[0]; 
			}   
			
			if (!empty($shippingCode)) { 
				$address = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress(); 
				if ($address->getCountryId() == '') $address->setCountryId($this->_country); 
				if ($address->getCity() == '') $address->setCity(''); 
				if ($address->getPostcode() == '') $address->setPostcode(''); 
				if ($address->getRegionId() == '') $address->setRegionId(''); 
				if ($address->getRegion() == '') $address->setRegion(''); 
				$address->setShippingMethod($this->_shippingCode)->setCollectShippingRates(true); 
				Mage::getSingleton('checkout/session')->getQuote()->save(); 
			} else { 
				$address = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress(); 
				if ($address->getCountryId() == '') $address->setCountryId($this->_country); 
				if ($address->getCity() == '') $address->setCity(''); 
				if ($address->getPostcode() == '') $address->setPostcode(''); 
				if ($address->getRegionId() == '') $address->setRegionId(''); 
				if ($address->getRegion() == '') $address->setRegion(''); 
				$address->setShippingMethod($this->_shippingCode)->setCollectShippingRates(true); 
				Mage::getSingleton('checkout/session')->getQuote()->save(); 
			}   
				Mage::getSingleton('checkout/session')->resetCheckout();   
		} 
		catch (Mage_Core_Exception $e) { 
			Mage::getSingleton('checkout/session')->addError($e->getMessage()); 
		} 
		catch (Exception $e) { 
				Mage::getSingleton('checkout/session')->addException($e, Mage::helper('checkout')->__('Load customer quote error')); 
		}     
	}   
		
	public function getQuote() { 
		if (empty($this->_quote)) { 
			$this->_quote = Mage::getSingleton('checkout/session')->getQuote(); 
		} 
		return $this->_quote; 
	}
	public function saveShippingMethodInSessionAction(){
		// get the select shipping method
		$method = $this->getRequest()->getPost('method');
		$oldGrandTotal = $this->getcartTotal()->getSubtotalWithDiscount();
		//print_r(get_class_m($this->getcartTotal()));
		//echo $oldGrandTotal;exit;
		if($method == "flatrate3_flatrate3"){
			$rate = Mage::getStoreConfig('carriers/flatrate3/price');
		}else if($method == "flatrate2_flatrate2"){
			$rate = Mage::getStoreConfig('carriers/flatrate2/price');
		}
		
		$newGrandTotal = $oldGrandTotal + $rate;
		$newGrandTotal = $formattedPrice = Mage::helper('core')->currency($newGrandTotal,true,false);
		try{
			// get the checkout session for storing shipping method
			
			$checkout_session = $this->_getCheckoutSession();
			$checkout_session->setShippingMethod($method);
			$this->_shippingCode = $method;
			$this->addShipping();
			$response = array(
				'status'=>1,
				'price'=>$newGrandTotal
			);
			echo json_encode($response);
		}catch(Exception $e){
			$response = array(
				'status'=>0,
				'error'=>$e->getMessage()
			);
			echo json_encode($response);
		}
	}
	public function setShippingMethodIfNot(){
		
		$checkout_session = $this->_getCheckoutSession();
		$method =  $checkout_session->getShippingMethod();
		$subTotal = $this->getcartTotal()->getSubtotalWithDiscount();
		if(Mage::app()->getStore()->getStoreId()==2){
		$method ="freeshipping_freeshipping";
		$checkout_session->setShippingMethod($method);
		}else{
		if (!isset($method) && ($subTotal < 100)){
			$method ="flatrate3_flatrate3";
			$checkout_session->setShippingMethod($method);
		}
		else if(!isset($method) && ($subTotal >= 100) ){
		
			$method ="flatrate2_flatrate2";
			$checkout_session->setShippingMethod($method);
		}
		else if(isset($method) && ($subTotal >= 100) ){
		
			$method ="flatrate2_flatrate2";
			$checkout_session->setShippingMethod($method);
			
		}
		
		}
		
		return $method;
	
	}
	
	public function saveBillingAction()
	{
		
		$method = $this->setShippingMethodIfNot();
		if ($this->_expireAjax()) {
			return;
		}
		if ($this->getRequest()->isPost()) {
			// $postData = $this->getRequest()->getPost('billing', array());
			// $data = $this->_filterPostData($postData);
			$data = $this->getRequest()->getPost('billing', array());
			$customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

			if (isset($data['email'])) {
				$data['email'] = trim($data['email']);
			}
			$result = $this->getOnepage()->saveBilling($data, $customerAddressId);

			if (!isset($result['error'])) {

				if ($this->getOnepage()->getQuote()->isVirtual() || isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {

					if(!$this->getOnepage()->getQuote()->isVirtual()){
		//$method = 'freeshipping_freeshipping';
						$result = $this->getOnepage()->saveShippingMethod($method);
					}

					if (!isset($result['error'])) {

						try{ 
							if(Mage::app()->getStore()->getStoreId()==2){
						$data = array('method'=>'cashondelivery');
						}else{
						$data = array('method'=>'barclays');
						}
							$result = $this->getOnepage()->savePayment($data);
							$redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
							if (empty($result['error']) && !$redirectUrl) {
								$this->loadLayout('checkout_onepage_review');
								$result['goto_section'] = 'review';
								$result['update_section'] = array(
				                    'name' => 'review',
				                    'html' => $this->_getReviewHtml()
								);
							}
							if ($redirectUrl) {
								$result['redirect'] = $redirectUrl;
							}
							if(!$this->getOnepage()->getQuote()->isVirtual()){
								$result['allow_sections'] = array('shipping');
								$result['duplicateBillingInfo'] = 'true';
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
					}
				} else {
					$result['goto_section'] = 'shipping';
				}
			}

			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}
	}

	
	public function saveShippingAction()
	{
		
		//echo $subTotal;exit;
		//echo "sdfsdf";var_dump($method);exit;
		$method =  $this->setShippingMethodIfNot();
		if ($this->_expireAjax()) {
			return;
		}
		if ($this->getRequest()->isPost()) {
			$data = $this->getRequest()->getPost('shipping', array());
			$customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
			$result = $this->getOnepage()->saveShipping($data, $customerAddressId);

			if (!isset($result['error'])) {
				//$method = 'freeshipping_freeshipping';
				$result = $this->getOnepage()->saveShippingMethod($method);
				
				if (!isset($result['error'])) {
					try{
						if(Mage::app()->getStore()->getStoreId()==2){
						$data = array('method'=>'cashondelivery');
						}else{
						$data = array('method'=>'barclays');
						}
						$result = $this->getOnepage()->savePayment($data);
						//print_r($this->getOnepage()->getQuote()->getPayment()); exit;
						
						$redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
						if (empty($result['error']) && !$redirectUrl) {
							$this->loadLayout('checkout_onepage_review');
							$result['goto_section'] = 'review';
							$result['update_section'] = array(
	                    'name' => 'review',
	                    'html' => $this->_getReviewHtml()
							);
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
				}
			}
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}
	}
}