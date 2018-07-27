<?php
require_once 'Mage/Checkout/controllers/CartController.php';
class Excellence_Ajaxcoupon_IndexController extends Mage_Checkout_CartController
{

	public function customcouponPostAction()
  {
    	/**
    	 * No reason continue with empty shopping cart
    	 */

    	$response = array();
    	$response['status'] = 'ERROR';
    	if (!$this->_getCart()->getQuote()->getItemsCount()) {
    		//$this->_goBack();
    		return;
    	}
   //echo $this->getRequest()->getParam('remove'); die;
    	$couponCode = (string) $this->getRequest()->getParam('coupon_code');

    	if ($this->getRequest()->getParam('remove') == 1) {
    		$couponCode = '';
    	}

      if (strpos($couponCode,'slap-') !== false || strpos($couponCode,'happy-') !== false) {
        $this->personSpecificCouponCheckoutAction();
            // $this->_goBack();
        return;
      }

      $oldCouponCode = $this->_getQuote()->getCouponCode();

      if (!strlen($couponCode) && !strlen($oldCouponCode)) {
    		//$this->_goBack();
        return;
      }

      try {
        $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
        $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
        ->collectTotals()
        ->save();

        if (strlen($couponCode)) {
         if ($couponCode == $this->_getQuote()->getCouponCode()) {
    				//$this->_getSession()->addSuccess(
    				//		$this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode))
    				//);
          $response['msg'] =$this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode));
          $response['status'] = 'SUCCESS';

        }
        else {
    				//$this->_getSession()->addError(
    				//		$this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
    				//);
          $response['msg'] = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode));
          $response['status'] = 'ERROR';
        }
      } else {
       $response['status'] = 'SUCCESS';
    			//$this->_getSession()->addSuccess($this->__('Coupon code was canceled.'));
       $response['msg'] = $this->__('Coupon code was canceled.');
     }

   } catch (Mage_Core_Exception $e) {
    		//$this->_getSession()->addError($e->getMessage());
   } catch (Exception $e) {
    		//$this->_getSession()->addError($this->__('Cannot apply the coupon code.'));
    		//Mage::logException($e);
    $response['msg'] = $this->__('Cannot apply the coupon code.');
  }
  $this->loadLayout(false);
  $review = $this->getLayout()->getBlock('roots')->toHtml();
  $response['review'] = $review;
    	//$this->_goBack();
  $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
}

      /*
        this function used to check person specific coupon on cart page
    */

        public function personSpecificCouponAction()
        {
          $couponCode = (string) $this->getRequest()->getParam('coupon_code');

          if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
          }

          $oldCouponCode = $this->_getQuote()->getCouponCode();

          if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            $this->_goBack();
            return;
          }
          $loginStatus = Mage::getSingleton('customer/session')->isLoggedIn();
          if($loginStatus){
           $customerData = Mage::getSingleton('customer/session')->getCustomer();

           if(strpos($couponCode,'slap-')!==false){
             $extractEmail = str_replace("slap-","",$couponCode);
           }

           if(strpos($couponCode,'happy-')!==false){
             $extractEmail = str_replace("happy-","",$couponCode);
           }

           if(trim($extractEmail) == $customerData->getEmail()){
            try {
              /* save coupon in qoute */
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
                  $this->_getSession()->addError($this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
                    );
                }
              }
              else {
                $this->_getSession()->addSuccess($this->__('Coupon code was canceled.'));
              }
            } catch (Mage_Core_Exception $e) {
              $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
              $this->_getSession()->addError($this->__('Coupon Cannot apply the coupon code.'));
              Mage::logException($e);
            }

          }else{
           $this->_getSession()->addError($this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
            );
         }

       }else{
         $this->_getSession()->addError($this->__('To Redeam Coupon Please login', Mage::helper('core')->htmlEscape($couponCode)));
       }
       $this->_goBack();
     }

      /*
        this function used to check person specific coupon on checkout page
    */

        public function personSpecificCouponCheckoutAction()
        {
          $couponCode = (string) $this->getRequest()->getParam('coupon_code');
          if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
          }

          $oldCouponCode = $this->_getQuote()->getCouponCode();

          if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            $this->_goBack();
            return;
          }
          $loginStatus = Mage::getSingleton('customer/session')->isLoggedIn();
          if($loginStatus){
           $customerData = Mage::getSingleton('customer/session')->getCustomer();

           if(strpos($couponCode,'slap-')!==false){
             $extractEmail = str_replace("slap-","",$couponCode);
           }

           if(strpos($couponCode,'happy-')!==false){
             $extractEmail = str_replace("happy-","",$couponCode);
           }

           if(trim($extractEmail) == $customerData->getEmail()){
            try {
              /* save coupon in qoute */
              $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
              $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
              ->collectTotals()
              ->save();

              if (strlen($couponCode)) {
                if ($couponCode == $this->_getQuote()->getCouponCode()) {
                  $response['msg'] =$this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode));
                  $response['status'] = 'SUCCESS';
                }
                else {
                  $response['msg'] = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode));
                  $response['status'] = 'ERROR';
                }
              }
              else {
               $response['msg'] =$this->__('Coupon code was canceled.');
               $response['status'] = 'SUCCESS';
             }
           } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
          } catch (Exception $e) {
            $response['msg'] = $this->__('Coupon Cannot apply the coupon code.');
            $response['status'] = 'ERROR';
            Mage::logException($e);
          }

        }else{
          $response['msg'] = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode));
          $response['status'] = 'ERROR';
        }

      }else{
       $response['msg'] = $this->__('To Redeam Coupon Please login', Mage::helper('core')->htmlEscape($couponCode));
       $response['status'] = 'ERROR';
     }
     $this->loadLayout(false);
     $review = $this->getLayout()->getBlock('roots')->toHtml();
     $response['review'] = $review;
     $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
   }

   protected function blackListEmail($cart){
    foreach (unserialize(Mage::getStoreConfig("blacklist_section/blacklist/blacklist_email")) as $mapping) {
      $cusemail = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
      if($mapping['email'] == $cusemail){
        foreach ($cart->getItems() as $itemdata){
          $itemid = $itemdata->getItemId();
          $this->_getCart()->removeItem($itemid)->save();
        }
        return 1;
      }
    }
  }

  public function indexAction(){
    $cart = $this->_getCart();
    if ($cart->getQuote()->getItemsCount()) {
      /*$cartemail = $this->blackListEmail($cart);
      if($cartemail == 1){
        $this->_getSession()->addError($this->__('You are not allowed to purchase any Product.'));
      }
      $cart->init();
      $cart->save();*/

      if (!$this->_getQuote()->validateMinimumAmount()) {
        $minimumAmount = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
        ->toCurrency(Mage::getStoreConfig('sales/minimum_order/amount'));

        $warning = Mage::getStoreConfig('sales/minimum_order/description')
        ? Mage::getStoreConfig('sales/minimum_order/description')
        : Mage::helper('checkout')->__('Minimum order amount is %s', $minimumAmount);

        $cart->getCheckoutSession()->addNotice($warning);
      }
    }

        // Compose array of messages to add
    $messages = array();
    foreach ($cart->getQuote()->getMessages() as $message) {
      if ($message) {
                // Escape HTML entities in quote message to prevent XSS
        $message->setCode(Mage::helper('core')->escapeHtml($message->getCode()));
        $messages[] = $message;
      }
    }
    $cart->getCheckoutSession()->addUniqueMessages($messages);

        /**
         * if customer enteres shopping cart we should mark quote
         * as modified bc he can has checkout page in another window.
         */
        $this->_getSession()->setCartWasUpdated(true);
        Varien_Profiler::start(__METHOD__ . 'cart_display');
        $this
        ->loadLayout()
        ->_initLayoutMessages('checkout/session')
        ->_initLayoutMessages('catalog/session');
           // ->getLayout()->getBlock('head')->setTitle($this->__('Shopping Cart'));
        $this->renderLayout();
        Varien_Profiler::stop(__METHOD__ . 'cart_display');

        /*****************custom code for page capture abandant start********************/

        if (Mage::app()->getFrontController()->getAction()->getFullActionName() == 'checkout_cart_index') {
          $quote_id = $this->_getQuote()->getId(); 
          $quote_model = Mage::getModel('sales/quote')->load($quote_id);
          $quote_model->setData('abandoned_page_capture','cartpage')->save();
          }

        /*****************custom code for page capture abandant end********************/  
      }

      public function addAction(){
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();
        $parentIdBundle = Mage::helper('ajaxcoupon')->getCartParentId();
        try {
          if (isset($params['qty'])) {
            $filter = new Zend_Filter_LocalizedToNormalized(
              array('locale' => Mage::app()->getLocale()->getLocaleCode())
              );
            $params['qty'] = $filter->filter($params['qty']);
          }
          $product = $this->_initProduct();
          $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
              $this->_goBack();
              return;
            }
            /*For bundle Product S*/
            if(in_array($params['product'], $parentIdBundle))
            { 
              /*Replace the old pack size with new pack size(Same Product)*/
                $removeProduct = Mage::helper('ajaxcoupon')->removeProduct($parentIdBundle,$params['product']);
                $cart->addProduct($product, $params);
            }else{
                  $cart->addProduct($product, $params);
            }
            /*For bundle Product E*/
            if (!empty($related)) {
              $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

/*****************custom code for page capture abandant start********************/

         /* $quote_id = $this->_getQuote()->getId(); 
          $quote_model = Mage::getModel('sales/quote')->load($quote_id);
          $quote_model->setData('abandoned_page_capture','cartpage')->save();*/
          

/*****************custom code for page capture abandant end********************/


            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
              array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
              );
            //$cartemail = $this->blackListEmail($cart);
            if (!$this->_getSession()->getNoCartRedirect(true)) {
              /*if (!$cart->getQuote()->getHasError() && $cartemail != 1){
                $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                $this->_getSession()->addSuccess($message);
                if(isset($params['item'])) {
                  Mage::getModel('wishlist/item')->load($params['item'])->delete();
                }
              }*/
              if (!$cart->getQuote()->getHasError()){
                $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                $this->_getSession()->addSuccess($message);
                if(isset($params['item'])) {
                  Mage::getModel('wishlist/item')->load($params['item'])->delete();
                }
              }
              $this->_goBack();
            }
          } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
              $this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
              $messages = array_unique(explode("\n", $e->getMessage()));
              foreach ($messages as $message) {
                $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
              }
            }

           /* start of maximum quantity redirect 404 issue */

            $helperdata = Mage::helper("ajaxcoupon/data");
            $parentId = $helperdata->getParentproduct($product->getSku());
            if($parentId){
              $url = $parentId->getProductUrl();
            }else{
              $url = $this->_getSession()->getRedirectUrl(true);

            }
            /* end of maximum quantity redirect 404 issue */

            if ($url) {
              $this->getResponse()->setRedirect($url);
            } else {
              $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
          } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            $this->_goBack();
          }
        }

      }
