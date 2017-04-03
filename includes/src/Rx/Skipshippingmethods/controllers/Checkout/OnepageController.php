<?php

require_once 'Mage/Checkout/controllers/OnepageController.php';
class Rx_Skipshippingmethods_Checkout_OnepageController extends Mage_Checkout_OnepageController
{
 
 protected $_sectionUpdateFunctions = array(
    'payment-method' => '_getPaymentMethodsHtml',
    // 'shipping-method' => '_getShippingMethodsHtml',
    'review' => '_getReviewHtml',
);

public function saveBillingAction()
{
   echo "asd";exit;
    if ($this->_expireAjax()) {
        return;
    }
    if ($this->getRequest()->isPost()) {
        $data = $this->getRequest()->getPost('billing', array());
        $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
        if (isset($data['email'])) {
            $data['email'] = trim($data['email']);
        
		}
		$method = 'flatrate_flatrate';
		Mage::getSingleton('checkout/type_onepage')->getQuote()->getShippingAddress()-> setShippingMethod($method)->save();
        $result = $this->getOnepage()->saveBilling($data, $customerAddressId);
		if (!isset($result['error'])) {
			if (!isset($result['error'])) {
				/* check quote for virtual */
				if ($this->getOnepage()->getQuote()->isVirtual()) {
					$result['goto_section'] = 'payment';
					$result['update_section'] = array(
					'name' => 'payment-method',
					'html' => $this->_getPaymentMethodsHtml()
					);
				}
				elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
					$result['allow_sections'] = array('shipping');
					$result['duplicateBillingInfo'] = 'true';
					$result['goto_section'] = 'payment';
				    $result['update_section'] = array(
					'name' => 'payment-method',
					'html' => $this->_getPaymentMethodsHtml()
					);
				}
				else {
					
					$result['goto_section'] = 'shipping';
					//$result['goto_section'] = 'payment';
				}
			}
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}
	}
} 
  public function saveShippingAction()
    { 
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);
			

            if (!isset($result['error'])) {
				$result['goto_section'] = 'payment';
				$result['update_section'] = array(
				'name' => 'payment-method',
				'html' => $this->_getPaymentMethodsHtml()
				);
			}
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

  
   /**
     * Shipping method save action
     */

    public function saveShippingMethodAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
			$method = 'flatrate_flatrate';
            //$result = $this->getOnepage()->saveShippingMethod('flatrate_flatrate');
			Mage::getSingleton('checkout/type_onepage')->getQuote()->getShippingAddress()-> setShippingMethod($method)->save();
            if(!$result) {
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method',
                        array('request'=>$this->getRequest(),
                            'quote'=>$this->getOnepage()->getQuote()));
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                
                if ($this->getOnepage()->getQuote()->getGrandTotal() == 0 && !$this->getOnepage()->getQuote()->hasRecurringItems()) {
                  $result = $this->getOnepage()->savePayment(array('method' => 'free'));
                  $this->loadLayout('checkout_onepage_review');
                  $result['goto_section'] = 'review';
                  $result['update_section'] = array(
                      'name' => 'review',
                      'html' => $this->_getReviewHtml()
                  );
                } else {

                  $result['goto_section'] = 'payment';
                  $result['update_section'] = array(
                      'name' => 'payment-method',
                      'html' => $this->_getPaymentMethodsHtml()
                  );
                }
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }
    /**
     * Create order action
     */
    //add function
	 public function getOrder()
    {
        if ($this->_order == null) {
            $session = Mage::getSingleton('checkout/session');
            $this->_order = Mage::getModel('sales/order');
            $this->_order->loadByIncrementId($session->getLastRealOrderId());
        }
        return $this->_order;
    }
	//end code
   public function saveOrderAction()
    {
	    //echo "u r here";exit;
        if ($this->_expireAjax()) {
            return;
        }

        $result = array();
        try {
            if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                if ($diff = array_diff($requiredAgreements, $postedAgreements)) {
				    
                    $result['success'] = false;
                    $result['error'] = true;
                    $result['error_messages'] = $this->__('Please agree to all the terms and conditions before placing the order.');
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                }
            }
            if ($data = $this->getRequest()->getPost('payment', false)) {
			
                $this->getOnepage()->getQuote()->getPayment()->importData($data);
            }
			
            $this->getOnepage()->saveOrder();
			
			//print_r(get_class_methods($retVal));exit;
			
		    $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
			
            $result['success'] = true;
            $result['error']   = false;
			
			$test = Mage::getSingleton('core/session')->getEcheckhell();
			if($test == "yes"){
				$baseUrl = Mage::getBaseUrl();
				$status_check = Mage::getSingleton('core/session')->getEcheckstatus();
				if($status_check[1] =="ERROR" ){
					
				 	$result['success'] = false;
					$result['error']   = true;
					$id=Mage::getSingleton('checkout/session')->getLastRealOrderId();
					$order = Mage::getModel('sales/order')->loadByIncrementId($id);
					$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
					
					Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(),'error');
					Mage::getSingleton('checkout/session')->unsLastRealOrderId();
					//$redirectUrl = 'http://reliablerx.iksuladev.com/checkout/onepage/failure';
					$redirectUrl = $baseUrl.'checkout/onepage/failure';
					
					Mage::getSingleton('core/session')->unsEcheckstatus();
					
				}
				else
				{
					$result['success'] = true;
					$result['error']   = false;
					$redirectUrl = $baseUrl.'checkout/onepage/echecksuccess';
					//$redirectUrl = 'http://reliablerx.iksuladev.com/checkout/onepage/echecksuccess';
					$order = $this->getOrder();
					$order->sendNewOrderEmail();
				}
				Mage::getSingleton('core/session')->unsEcheckhell();
				
			}
			
			
        } catch (Mage_Payment_Model_Info_Exception $e) {
            $message = $e->getMessage();
			
            if( !empty($message) ) {
                $result['error_messages'] = $message;
            }
            $result['goto_section'] = 'payment';
            $result['update_section'] = array(
                'name' => 'payment-method',
                'html' => $this->_getPaymentMethodsHtml()
            );
			
        } catch (Mage_Core_Exception $e) {
			
            Mage::logException($e);
            //Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = $e->getMessage();

            if ($gotoSection = $this->getOnepage()->getCheckout()->getGotoSection()) {
                $result['goto_section'] = $gotoSection;
                $this->getOnepage()->getCheckout()->setGotoSection(null);
            }

            if ($updateSection = $this->getOnepage()->getCheckout()->getUpdateSection()) {
                if (isset($this->_sectionUpdateFunctions[$updateSection])) {
                    $updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
                    $result['update_section'] = array(
                        'name' => $updateSection,
                        'html' => $this->$updateSectionFunction()
                    );
                }
                $this->getOnepage()->getCheckout()->setUpdateSection(null);
            }
			
        } catch (Exception $e) {
		   
            Mage::logException($e);
            //Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success']  = false;
            $result['error']    = true;
            $result['error_messages'] = $this->__('There was an error processing your order. Please contact us or try again later.');
        }
        $this->getOnepage()->getQuote()->save();
        /**
         * when there is redirect to third party, we don't want to save order yet.
         * we will save the order in return action.
         */
        if (isset($redirectUrl)) {
            $result['redirect'] = $redirectUrl;
        }
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        
    }
	public function echecksuccessAction()
	{
	
		$lastQuoteId = $this->getOnepage()->getCheckout()->getLastQuoteId();
        $lastOrderId = $this->getOnepage()->getCheckout()->getLastOrderId();

        if (!$lastQuoteId || !$lastOrderId) {
            $this->_redirect('checkout/cart');
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
	}
	public function worksfailureAction()
    {
	 
		Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(),'error');
		Mage::getSingleton('checkout/session')->unsLastRealOrderId();
		$this->_redirect('checkout/onepage/failure');
    }
	
}

?>