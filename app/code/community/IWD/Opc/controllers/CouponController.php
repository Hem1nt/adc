<?php

class IWD_Opc_CouponController extends Mage_Core_Controller_Front_Action{

	const XML_PATH_DEFAULT_PAYMENT = 'opc/default/payment';

	/**
	 * Retrieve shopping cart model object
	 *
	 * @return Mage_Checkout_Model_Cart
	 */
	protected function _getCart(){
		return Mage::getSingleton('checkout/cart');
	}
	
	/**
	 * Get checkout session model instance
	 *
	 * @return Mage_Checkout_Model_Session
	 */
	protected function _getSession(){
		return Mage::getSingleton('checkout/session');
	}
	
	/**
	 * Get current active quote instance
	 *
	 * @return Mage_Sales_Model_Quote
	 */
	protected function _getQuote(){
		return $this->_getCart()->getQuote();
	}
	
	
	/**
	 * Get payments method step html
	 *
	 * @return string
	 */
	protected function _getPaymentMethodsHtml($use_method = false){
	
		/** UPDATE PAYMENT METHOD **/
		// check what method to use
		$apply_method = Mage::getStoreConfig(self::XML_PATH_DEFAULT_PAYMENT);
		if($use_method)
			$apply_method = $use_method;
		
		$_cart = $this->_getCart();
		$_quote = $_cart->getQuote();
		$_quote->getPayment()->setMethod($apply_method);
		$_quote->setTotalsCollectedFlag(false)->collectTotals();
		$_quote->save();
	
		$layout = $this->getLayout();
		$update = $layout->getUpdate();
		$update->load('checkout_onepage_paymentmethod');
		$layout->generateXml();
		$layout->generateBlocks();
		
		$output = $layout->getOutput();
		return $output;
	}
	
	public function couponPostAction(){
		
		$responseData = array();
		/**
		 * No reason continue with empty shopping cart
		 */
		if (!$this->_getCart()->getQuote()->getItemsCount()) {
			$this->_redirect('checkout/cart');
			return;
		}

	
		$couponCode = (string) $this->getRequest()->getParam('coupon_code');
		if ($this->getRequest()->getParam('remove') == 1) {
			$couponCode = '';
		}

		$oldCouponCode = $this->_getQuote()->getCouponCode();

		if (strpos($couponCode,'HB-') !== false) {
        	return $this->personSpecificCouponCheckoutAction($couponCode,$oldCouponCode);
      	}

      	if (strpos($couponCode,'CB-') !== false) {
        	$moduleStatus = Mage::helper('cashback')->isEnable();
        	if($moduleStatus){
				return $this->cashbackCouponCheckoutAction($couponCode,$oldCouponCode);
        	}
      	}

	
		/*if (!strlen($couponCode) && !strlen($oldCouponCode)) {
			$responseData['message'] = $this->__('Coupon code is not valid.');
			$this->getResponse()->setHeader('Content-type','application/json', true);
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
			return;
		}*/
	
		/// get list of available methods before discount changes
		$methods_before = Mage::helper('opc')->getAvailablePaymentMethods();
		///////

		try {
			$this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
			$this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
				->collectTotals()
				->save();
	
			/// get list of available methods after discount changes
			$methods_after = Mage::helper('opc')->getAvailablePaymentMethods();
			///////
			
			if ($couponCode) {
				if ($couponCode == $this->_getQuote()->getCouponCode()) {
					$responseData['message'] = $this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode));
				}else {			
					$responseData['message'] = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode));					
				}
			} else {
				$responseData['message'] =  $this->__('Coupon code was canceled.');
			}
			
			$layout= $this->getLayout();
			$block = $layout->createBlock('checkout/cart_coupon');
			$block->setTemplate('opc/onepage/coupon.phtml');
			$responseData['coupon'] = $block->toHtml();
			
			// check if need to reload payment methods
			$use_method = Mage::helper('opc')->checkUpdatedPaymentMethods($methods_before, $methods_after);
			if($use_method != -1)
				$responseData['payments'] = $this->_getPaymentMethodsHtml($use_method);
			/////
			
		} catch (Mage_Core_Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		} catch (Exception $e) {
			$responseData['message'] =  $this->__('Cannot apply the coupon code.');
			Mage::logException($e);
		}
		
		
		$this->getResponse()->setHeader('Content-type','application/json', true);
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
		
	}

	public function personSpecificCouponCheckoutAction($couponCode,$oldCouponCode)
        {
          $currentTimestamp = Mage::getModel('core/date')->timestamp(time());
          $currentDate = date('Y-m-d h:i:s', $currentTimestamp);
          // $couponCode = (string) $this->getRequest()->getParam('coupon_code');
          // if($this->getRequest()->getParam('remove') == 1) {
          //   $couponCode = '';
          // }

          // $oldCouponCode = $this->_getQuote()->getCouponCode();

          if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            	$this->_goBack();
            	return;
          }

          $loginStatus = Mage::getSingleton('customer/session')->isLoggedIn();
          if($loginStatus){
          		$customerData = Mage::getSingleton('customer/session')->getCustomer();
          		$model = Mage::getModel('birthday/birthday')->getCollection();
          		$model->addFieldToFilter('coupon', $couponCode);

          		$offerValue = $model->getFirstItem()->getData();
          		// print_r($offerValue);exit;
					           		
           		if(!empty($offerValue)){
           			// foreach ($offerData as $offerValue) {
       			    	if($offerValue['email'] == $customerData['email']){
	           			if($offerValue['coupon_created_date']<=$currentDate && $offerValue['coupon_expire_date']>=$currentDate){
	           				try {
					              /* save coupon in qoute */
					                $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
									$this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
									->collectTotals()
									->save();
									// if coupon code exits in code 

					              	if (strlen($couponCode)) {
						                if($couponCode == $this->_getQuote()->getCouponCode()) {
						                  $response['message'] =$this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode));
						                }
						                else {
						                  $response['message'] = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode));
						                }
					              	}
					              	else{
						               	$response['message'] =$this->__('Coupon code was canceled.');
						            }
						            $layout= $this->getLayout();
									$block = $layout->createBlock('checkout/cart_coupon');
									$block->setTemplate('opc/onepage/coupon.phtml');
									$response['coupon'] = $block->toHtml();

					           }
					           catch (Mage_Core_Exception $e) {
					          		$this->_getSession()->addError($e->getMessage());
					           }
					           catch (Exception $e) {
					           		$response['message'] = $this->__('Coupon Cannot apply the coupon code.');
					           		Mage::logException($e);
					           }
	           			}	
	           			else{
	           					$response['message'] =$this->__('Coupon code "%s" is expired.', Mage::helper('core')->htmlEscape($couponCode));
	           			}
	           		}
	           		else{
	           			$response['message'] =$this->__('Registerd Email Id is different.');
	           		}
           		// }
           	 
           }
           else{
           		$response['message'] = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode));
           }
        }
        else{
	       $response['message'] = $this->__('To Redeam Coupon Please login', Mage::helper('core')->htmlEscape($couponCode));
	      }
	     $this->loadLayout(false);
	     //$review = $this->getLayout()->getBlock('roots')->toHtml();
	     //$response['review'] = $review;
	     $this->getResponse()->setHeader('Content-type','application/json', true);
		 $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
   }

   public function cashbackCouponCheckoutAction($couponCode,$oldCouponCode)
        {
          if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            	$this->_goBack();
            	return;
          }
          $loginStatus = Mage::getSingleton('customer/session')->isLoggedIn();
          if($loginStatus){
          	    $useMonth = date('MY');
          	    $moduleStatus = Mage::helper('cashback')->isEnable();
          		$customerData = Mage::getSingleton('customer/session')->getCustomer();
          		$model = Mage::getModel('cashback/cashback')->getCollection();
          		$model->addFieldToFilter('coupon_code', $couponCode);
          		$model->addFieldToFilter('uses', 0);

          		$offerValue = $model->getFirstItem()->getData();
	        	$minimumCartValue = Mage::helper('cashback')->minimumCartValue();

	        	$cashbackCollection = Mage::getModel('cashback/cashback')->getCollection()
	        	                     ->addFieldToFilter('user_email', $offerValue['user_email'])
	        	                     ->addFieldToFilter('use_month', $useMonth)
	        	                     ->addFieldToFilter('uses', '1');
                $useMonthCout = $cashbackCollection->count();

				$subtotal = $this->_getQuote()->getSubtotal();	           		
           		if($moduleStatus){
	           		if(!empty($offerValue)){
	       			    if($offerValue['user_email'] == $customerData['email']){
	           				if ($useMonthCout <= 0) {
	       			    		if($subtotal >= $minimumCartValue){
			           				try {
							              /* save coupon in qoute */
							                $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
											$this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
											->collectTotals()
											->save();
											// if coupon code exits in code 

							              	if (strlen($couponCode)) {
								                if($couponCode == $this->_getQuote()->getCouponCode()) {
								                  $response['message'] =$this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode));
								                  $response['sucessStatus'] = '1';
								                }
								                else {
								                  $response['message'] = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode));
								                }
							              	}
							              	else{
								               	$response['message'] =$this->__('Coupon code was canceled.');
								            }
								            $layout= $this->getLayout();
											$block = $layout->createBlock('checkout/cart_coupon');
											$block->setTemplate('opc/onepage/coupon.phtml');
											$response['coupon'] = $block->toHtml();

							           }
							           catch (Mage_Core_Exception $e) {
							          		$this->_getSession()->addError($e->getMessage());
							           }
							           catch (Exception $e) {
							           		$response['message'] = $this->__('Coupon Cannot apply the coupon code.');
							           		Mage::logException($e);
							           }
	       			    		}else{
	       			    			$addValue = $minimumCartValue - $subtotal;
	       			    			$response['message'] =$this->__('Add more $'.$addValue.' value product to use this coupon.');
	       			    		}
		           		}
		           		else{
		           			$response['message'] = $this->__('You can use only one cashback coupon per month.');
		           		}
	           		}else{
		           			$response['message'] = $this->__('Registerd Email Id is different.');
		           		}
	           	 
	           }
	           else{
	           		$response['message'] = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode));
	           }
           }
           else{
           		$response['message'] = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode));
           }
        }
        else{
	       $response['message'] = $this->__('To Redeam Coupon Please login', Mage::helper('core')->htmlEscape($couponCode));
	    }
	     $this->loadLayout(false);
	     //$review = $this->getLayout()->getBlock('roots')->toHtml();
	     //$response['review'] = $review;
	     $this->getResponse()->setHeader('Content-type','application/json', true);
		 $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
		 return $response;
   }

   public function newCouponPostAction()
    {
        /**
         * No reason continue with empty shopping cart
         */
        if (!$this->_getCart()->getQuote()->getItemsCount()) {
            $this->_redirect('checkout/cart/index');
            return;
        }

        $couponCode = (string) $this->getRequest()->getParam('coupon_code');
        if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }
        $oldCouponCode = $this->_getQuote()->getCouponCode();

        if (strpos($couponCode,'CB-') !== false) {
        	$moduleStatus = Mage::helper('cashback')->isEnable();
        	if($moduleStatus){
				return $this->cashbackNewCouponCheckoutAction($couponCode,$oldCouponCode);
        	}
      	}

        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            $this->_redirect('checkout/cart/index');
            return;
        }

        try {
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                ->collectTotals()
                ->save();

            if (strlen($couponCode)) {
                if ($couponCode == $this->_getQuote()->getCouponCode()) {
                    $this->_getSession()->addSuccess(
                        $this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode))
                    );
                }
                else {
                    $this->_getSession()->addError(
                        $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
                    );
                }
            } else {
                $this->_getSession()->addSuccess($this->__('Coupon code was canceled.'));
            }

        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Cannot apply the coupon code.'));
            Mage::logException($e);
        }

        $this->_redirect('checkout/cart/index');
    }

    public function cashbackNewCouponCheckoutAction($couponCode,$oldCouponCode)
        {
          if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            	$this->_goBack();
            	return;
          }
          $loginStatus = Mage::getSingleton('customer/session')->isLoggedIn();
          if($loginStatus){
          		$customerData = Mage::getSingleton('customer/session')->getCustomer();
          		$model = Mage::getModel('cashback/cashback')->getCollection();
          		$model->addFieldToFilter('coupon_code', $couponCode);
          		$model->addFieldToFilter('uses', 0);

          		$offerValue = $model->getFirstItem()->getData();
	        	$minimumCartValue = Mage::helper('cashback')->minimumCartValue();

	        	$moduleStatus = Mage::helper('cashback')->isEnable();
	        	$useMonth = date('MY');
	        	$cashbackCollection = Mage::getModel('cashback/cashback')->getCollection()
	        	                     ->addFieldToFilter('user_email', $offerValue['user_email'])
	        	                     ->addFieldToFilter('use_month', $useMonth)
	        	                     ->addFieldToFilter('uses', '1');
                $useMonthCout = $cashbackCollection->count();

				$subtotal = $this->_getQuote()->getSubtotal();	           		
           		if($moduleStatus){
	           		if(!empty($offerValue)){
	       			    if($offerValue['user_email'] == $customerData['email']){
	           				if ($useMonthCout <= 0) {
	       			    		if($subtotal >= $minimumCartValue){
			           				try {
							              /* save coupon in qoute */
							                $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
											$this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
											->collectTotals()
											->save();
											// if coupon code exits in code 

							              	if (strlen($couponCode)) {
								                if($couponCode == $this->_getQuote()->getCouponCode()) {
								                  $this->_getSession()->addSuccess(
								                       $this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode))
								                   );
								                }
								                else {
								                  $this->_getSession()->addError(
								                        $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
								                    );
								                }
							              	}
							              	else{
								               	$this->_getSession()->addSuccess(
							                        $this->__('Coupon code was canceled.')
							                    );
								            }
								            $layout= $this->getLayout();
											$block = $layout->createBlock('checkout/cart_coupon');
											$block->setTemplate('opc/onepage/coupon.phtml');
											$response['coupon'] = $block->toHtml();

							           }
							           catch (Mage_Core_Exception $e) {
							          		$this->_getSession()->addError($e->getMessage());
							           }
							           catch (Exception $e) {
							           		$this->_getSession()->addError(
								                        $this->__('Coupon Cannot apply the coupon code.')
								                    );
							           		Mage::logException($e);
							           }
	       			    		}else{
	       			    			$addValue = $minimumCartValue - $subtotal;
	       			    			$this->_getSession()->addError(
								                        $this->__('Add more "$%s" value product to use this coupon.', Mage::helper('core')->htmlEscape($addValue))
								                    );
	       			    		}
		           		}
		           		else{
		           			$this->_getSession()->addError(
								                        $this->__('You can use only one cashback coupon per month.')
								                    );
		           		}
	           		}else{
		           			$this->_getSession()->addError($this->__('Registerd Email Id is different.'));
		           		}
	           	 
	           }
	           else{
	           		$this->_getSession()->addError($this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode)));
	           }
           }
           else{
           		$this->_getSession()->addError($this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode)));
           }
        }
        else{
	       $this->_getSession()->addError($this->__('To Redeam Coupon Please login', Mage::helper('core')->htmlEscape($couponCode)));
	    }
	     $this->_redirect('checkout/cart/index');
   }
}