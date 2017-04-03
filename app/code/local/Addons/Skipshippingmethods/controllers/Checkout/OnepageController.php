<?php

require_once 'Mage/Checkout/controllers/OnepageController.php';

class Addons_Skipshippingmethods_Checkout_OnepageController extends Mage_Checkout_OnepageController
{
    public function doSomestuffAction()
    {
		if(true) {
			$result['update_section'] = array(
            	'name' => 'payment-method',
                'html' => $this->_getPaymentMethodsHtml()
			);					
		}
    	else {
			$result['goto_section'] = 'shipping';
		}		
    }    
protected $_sectionUpdateFunctions = array(
    'payment-method' => '_getPaymentMethodsHtml',
    // 'shipping-method' => '_getShippingMethodsHtml',
    'review' => '_getReviewHtml',
);

protected function blackListPhoneNumber($data){
    foreach (unserialize(Mage::getStoreConfig("blacklist_section/blacklist/blacklist_phonenumber")) as $mapping) {
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
        }
    }
}

protected function blackListAddress($data){
    foreach (unserialize(Mage::getStoreConfig("blacklist_section/blacklist/blacklist_address")) as $mapping) {
        if ((strtolower($data["street"][0]) == strtolower($mapping['address1'])) && (strtolower($data["street"][1]) == strtolower($mapping['address2'])) && (strtolower($data["country_id"]) == strtolower($mapping['country'])) && (strtolower($data["city"]) == strtolower($mapping['city'])) && (strtolower($data["postcode"]) == strtolower($mapping['zipcode']))){
            $session= Mage::getSingleton('checkout/session');
            $quote = $session->getQuote();
            $cart = Mage::getModel('checkout/cart');
            $cartItems = $cart->getItems();
            foreach ($cartItems as $item){
                $quote->removeItem($item->getId())->save();
            }
            // throw new Mage_Payment_Model_Info_Exception(Mage::helper('checkout')->__('An error occured and this is the message'));
            Mage::getModel('core/message_collection')->add(Mage::getSingleton('core/message')->error($message));
        }
    } 
}

protected function blackListEmail($data){
    $cusemail = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
    foreach (unserialize(Mage::getStoreConfig("blacklist_section/blacklist/blacklist_email")) as $mapping) {
        if($mapping['email'] == $data["email"] || $mapping['email'] == $cusemail){
            $session= Mage::getSingleton('checkout/session');
            $quote = $session->getQuote();
            $cart = Mage::getModel('checkout/cart');
            $cartItems = $cart->getItems();
            foreach ($cartItems as $item){
                $quote->removeItem($item->getId())->save();
            }
            // throw new Mage_Payment_Model_Info_Exception(Mage::helper('checkout')->__('An error occured and this is the message'));
            Mage::getModel('core/message_collection')->add(Mage::getSingleton('core/message')->error($message));
        }
    }
}

public function saveBillingAction()
{
   
    if ($this->_expireAjax()) {
        return;
    }
    if ($this->getRequest()->isPost()) {
        $data = $this->getRequest()->getPost('billing', array());
        $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
        if (isset($data['email'])) {
            $data['email'] = trim($data['email']);        
        }

        $this->blackListPhoneNumber($data);
        $this->blackListAddress($data);
        $this->blackListEmail($data);

        /*---------------- start of coupon for first order referral system-----------*/
        $cartObj =  Mage::getModel('checkout/session')->getQuote();
        $diff =$cartObj->getData('subtotal')-$cartObj->getData('subtotal_with_discount');
        $coupon = Mage::getModel('checkout/session')->getQuote()->getCouponCode();
        Mage::getModel('checkout/session')->getQuote()->getCouponAmount();
        if($coupon=='Referral_Discount'){
             $couponcode = 'Referral_Discount';
             Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode($couponcode)->collectTotals()->save();
        }
        $session=Mage::getSingleton('customer/session', array('name'=>'frontend') );

        if ($session->isLoggedIn()) {            
            $useremail = $session->getCustomer()->getEmail();
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
        } 
        
        /*---------------- end of coupon for first order refereal system-----------*/
        $method = 'tablerate_bestway';
        Mage::getSingleton('checkout/type_onepage')->getQuote()->getShippingAddress()->setShippingMethod($method)->save();
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
        }else{
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

            $this->blackListPhoneNumber($data);
            $this->blackListAddress($data);
            $this->blackListEmail($data);

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

  
    public function savePaymentAction_backup_23_4_15()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('payment', array());
            /*
            * first to check payment information entered is correct or not
            */

            try {
                $result = $this->getOnepage()->savePayment($data);
            }
            catch (Mage_Payment_Exception $e) {
                if ($e->getFields()) {
                    $result['fields'] = $e->getFields();
                }
                $result['error'] = $e->getMessage();
            }
            catch (Exception $e) {
                $result['error'] = $e->getMessage();
            }
            $redirectUrl = $this->getOnePage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (empty($result['error']) && !$redirectUrl) {
				$this->loadLayout('checkout_onepage_heared4us');

                $result['goto_section'] = 'heared4us';
            }

            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }

     public function savePaymentAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('payment', array());
            /*
            * first to check payment information entered is correct or not
            */

            try {
                $result = $this->getOnepage()->savePayment($data);
            }
            catch (Mage_Payment_Exception $e) {
                if ($e->getFields()) {
                    $result['fields'] = $e->getFields();
                }
                $result['error'] = $e->getMessage();
            }
            catch (Exception $e) {
                $result['error'] = $e->getMessage();
            }
            
            $redirectUrl = $this->getOnePage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (!$redirectUrl) {
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

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }
    public function saveHeared4usAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            
        	//Grab the submited value heared for us value
        	$_inchoo_Heared4us = $this->getRequest()->getPost('getvoice');
			Mage::getSingleton('core/session')->setInchooHeared4us($_inchoo_Heared4us);

			$result = array();
            
            $redirectUrl = $this->getOnePage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (!$redirectUrl) {
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

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }
    public function saveShippingMethodAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            $method = 'tablerate_bestway';
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
            
            // $this->getOnepage()->saveOrder();
             // Testinmg for error
            try{
                 $this->getOnepage()->saveOrder();
                 Mage::log('save Order', null, 'stepcheckoutlog.log');
            }catch(Exception $e){
                 $message = $e->getMessage();//exit;
                 Mage::log($message, null, 'checkoutlog.log');
                 $baseUrl = Mage::getBaseUrl();
                 $redirectUrl = $baseUrl.'checkout/onepage/failure';
            }
            
            //print_r(get_class_methods($retVal));exit;
            
            $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
           
            $result['success'] = true;
            $result['error']   = false;
            
            //add code
                   $id=Mage::getSingleton('checkout/session')->getLastRealOrderId();
                    Mage::log('redirect url-----------------'.$id, null, 'stepcheckoutlog.log');
                    $order = Mage::getModel('sales/order')->loadByIncrementId($id);
                    $payment = Mage::app()->getRequest()->getParams();
                    $sendmailFlag = 0;
                    if($payment['payment']['method'] == 'checkmo')
                    {
                       
                        $sendmailFlag = 1;
                    }
                    elseif($payment['payment']['method'] == 'wiretransfer')
                    {
                       
                        $sendmailFlag = 1;
                    }
                    if($sendmailFlag == '1')
                    {
                        $orderId = array();
                        $orderId[] = $order->getEntityId();
                        
                       //$statusObj = new Amasty_Oaction_Model_Command_Status();
                       //$success=$statusObj->execute($orderId,'awaiting_check_transfer');
                        // $order->sendNewOrderEmail();
                    }
                    
                    //Prescription-----------------
                    $filename = Mage::getSingleton('checkout/session')->getPrescription();
                    
                    if($filename != "")
                    {
                          $order->setOrderPrescription($filename);
                          $order->save();
                          Mage::getSingleton('checkout/session')->unsPrescription();
                    }   
                
                //echo $medical_history;exit;
                $val = Mage::getSingleton('core/session')->getMedicalrow();
                if($val == 'yes')
                {
                    $medicalhistoryObj = Mage::getModel('medicalhistory/medicalhistory');
                    $row=$medicalhistoryObj->getCollection();
                                             
                    foreach($row as $r)
                    {
                       $r1 = $r->getData('id');
                    }
                    if($r1 != "")
                    {
                      $order->setMedicalHistory($r1);
                      $order->save();
                     
                    }
                    Mage::getSingleton('core/session')->unsMedicalrow();
                }
                
                 //$order->save();
            //endcode
            
            $test = Mage::getSingleton('core/session')->getEcheckhell();
            if($test == "yes"){
                $baseUrl = Mage::getBaseUrl();
                $status_check = Mage::getSingleton('core/session')->getEcheckstatus();
                if($status_check == "1"){
                     Mage::log('echeck hell'.$id, null, 'stepcheckoutlog.log');
                    $result['success'] = false;
                    $result['error']   = true;
                    $id=Mage::getSingleton('checkout/session')->getLastRealOrderId();
                    $order = Mage::getModel('sales/order')->loadByIncrementId($id);
                    
                    //$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
                    
                    //Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(),'error');
                    // Mage::getSingleton('checkout/session')->unsLastRealOrderId();
                    
                    $redirectUrl = $baseUrl.'checkout/onepage/failure';
                    
                    Mage::getSingleton('core/session')->unsEcheckstatus();
                    
                }
                else
                {
                    $result['success'] = false;
                    $result['error']   = true;
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
                    Mage::getSingleton('core/session')->setEchecksuccess("1");
                    Mage::getSingleton('core/session')->unsEcheckstatus();
                    $order = $this->getOrder();
                    // $order->sendNewOrderEmail();
                }
                Mage::getSingleton('core/session')->unsEcheckhell();
                
            }
            $echeckmessage = Mage::getSingleton('core/session')->getecheckcardResponce();
            $lamdaSession = Mage::getSingleton('core/session')->getLamda();
             if($lamdaSession) {
                // echo "If"; exit;
                // print_r(Mage::getSingleton('core/session')->getLamda()); exit;
                $baseUrl = Mage::getBaseUrl();
                $redirectUrl = $baseUrl.'gspay/processing/payment/';
            }elseif($echeckmessage =='FAIL' && Mage::getSingleton('core/session')->getLamda()===NULL ){
                    // echo "Else"; exit;
                    Mage::log('echeck fail----------'.$id, null, 'stepcheckoutlog.log');
                    $result['success'] = false;
                    $result['error']   = true;
                    $id=Mage::getSingleton('checkout/session')->getLastRealOrderId();
                    $order = Mage::getModel('sales/order')->loadByIncrementId($id);
                    // $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
                    
                    // Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(),'error');
                    // Mage::getSingleton('checkout/session')->unsLastRealOrderId();
                    $baseUrl = Mage::getBaseUrl();
                    $redirectUrl = $baseUrl.'checkout/onepage/failure';
            }
            /*Start of Credit Card System in ADC */
            $baseUrl = Mage::getBaseUrl();
            $id = Mage::getSingleton('checkout/session')->getLastRealOrderId();
            $order = Mage::getModel('sales/order')->loadByIncrementId($id);

            $paymentMenthodUsed = Mage::getSingleton('core/session')->getPaymentMethosUsed();//exit;
            Mage::getSingleton('core/session')->setcustomOrderid($id);//exit;

            switch ($paymentMenthodUsed) {
                case 'echeck':
                                /*------------------Start of Echeck Credit Card System -------------*/
                                $echeckmessage = Mage::getSingleton('core/session')->getecheckcardResponce();
                                // exit;
                                // $echeckmessage = $result['responce'];
                                 Mage::log('echeck creditcard start-----------'.$id.'#####'.$echeckmessage, null, 'echecksteplog.log');
                                if($echeckmessage =='FAIL'){
                                 Mage::log('echeck creditcard inside loop-----------'.$id.'#####'.$echeckmessage, null, 'echecksteplog.log');
                                    // $result['success']  = false;
                                    // $result['error']    = true;
                                    // Mage::getSingleton('checkout/session')->unsLastRealOrderId();
                                    Mage::getSingleton('core/session')->setecheckcardResponce($echeckmessageresult);
                                    Mage::getSingleton('core/session')->setPaymentMethosUsed('echeck');
                                    // $orderState = "transaction_declined";
                                    // $orderStatus = "transaction_declined";
                                    // $order->setState($orderState, $orderStatus, "", true);
                                    // $order->save();
                                   // $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
                                   $baseUrl = Mage::getBaseUrl();
                                   $redirectUrl = $baseUrl.'checkout/onepage/failure';
                                   Mage::log('echeck creditcard end set failure---------'.$id, null, 'echecksteplog.log');

                                }
                                /*------------------End of Echeck Credit Card System -------------*/   

                    break;
                case 'anytrans':
                            
                            /*------------------Start of Anytrans Credit Card System -------------*/
                            $AnytranceRedirectUrl = Mage::getSingleton('core/session')->getAnytranceRedirectUrl();
                            Mage::log($id.'----------'.$AnytranceRedirectUrl, null, 'anytranscheckout.log');
                            if($AnytranceRedirectUrl){
                                Mage::log('anytrans creditcard start-----------'.$id, null, 'anytranssteplog.log');
                                $result['success']  = false;
                                $result['error']    = true;
                                // $redirectUrl = $AnytranceRedirectUrl;
                                Mage::getSingleton('core/session')->setPaymentMethosUsed('anytrans');

                                $order = Mage::getModel('sales/order')->loadByIncrementId($id);
                                $last_order_id = $order->getId();
                                $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
                                $connection->beginTransaction();
                                $msg = 'Payment Placed with anyBilling';
                                $delete = $connection->query("DELETE FROM sales_flat_order_status_history WHERE parent_id=".$last_order_id);
                                $orderState = "transaction_declined";
                                $orderStatus = "transaction_declined";
                                $order->setState($orderState, $orderStatus, "", true);
                                $order->addStatusToHistory($order->getStatus(),$msg , true);
                                $order->save();

                                Mage::getSingleton('core/session')->setcustomOrderid($id);
                                Mage::getSingleton('core/session')->unsAnytranceRedirectUrl($id);  
                                Mage::log('anytrans creditcard end-------------'.$id, null, 'anytranssteplog.log');
                                $baseUrl = Mage::getBaseUrl();
                                $redirectUrl = $baseUrl.'checkout/onepage/failure';                 
                            }
                            /*------------------End of Anytrans Credit Card System -------------*/    
                    break;
                
                case 'gspay':
                            /*------------------Start of Gspay Credit Card System -------------*/
                            $gspayRedirectUrl = Mage::getSingleton('core/session')->getGspayCCRedirectUrl();
                            $verifyPayment  = Mage::getSingleton('core/session')->getVerifyPayment();

                            Mage::getSingleton('core/session')->unsVerifyPayment();
                            Mage::log($verifyPayment, null, 'gspayverify-onepagecheckout.log');
                            Mage::log($id.'--'.$gspayRedirectUrl, null, 'gspayverify-session.log'); 
                            
                            if($gspayRedirectUrl){

                               /* patch for start order id is not going to checkout page */
                               $id = Mage::getSingleton('core/session')->getCustomIncrementId();
                               $order = Mage::getModel('sales/order')->loadByIncrementId($id);
                               
                               Mage::getSingleton('core/session')->unsCustomIncrementId();
                               /* patch for end order id is not going to checkout page */

                              Mage::log('gspay creditcard start-----------'.$id, null, 'gspaystep.log');
                              $result['success']  = false;
                              $result['error']    = true;
                              $redirectUrl = $gspayRedirectUrl;
                              
                              Mage::getSingleton('core/session')->setPaymentMethosUsed('gspay');

                              $last_order_id = $order->getId();
                              $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
                              $connection->beginTransaction();
                              $msg = 'Payment Placed with Gspay';
                              $delete = $connection->query("DELETE FROM sales_flat_order_status_history WHERE parent_id=".$last_order_id);
                              $orderState = "transaction_declined";
                              $orderStatus = "transaction_declined";
                              $order->setState($orderState, $orderStatus, "", true);
                              $order->addStatusToHistory($order->getStatus(),$msg , true);
                              $order->save();
                              
                              // $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
                              Mage::getSingleton('core/session')->unsGspayCCRedirectUrl();  
                              Mage::log('gspay creditcard gspay-------------'.$id, null, 'gspaystep.log'); 
                              $baseUrl = Mage::getBaseUrl();
                              $redirectUrl = $baseUrl.'checkout/onepage/failure';                         
                            }
                            /*------------------End of Gspay Credit Card System -------------*/ 
                break;
                
                default:
                 break;
            }
             /*End of Credit Card System in ADC */
            
            
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
            Mage::log($e.'----------'.$AnytranceRedirectUrl, null, 'anytranscheckout.log');
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
              Mage::log('save quote    ----------'.$id, null, 'stepcheckoutlog.log');
        /**
         * when there is redirect to third party, we don't want to save order yet.
         * we will save the order in return action.
         */
        if (isset($redirectUrl)) {
            $result['redirect'] = $redirectUrl;
        }
            Mage::log('after redirect set ---------  '.$id.''.$redirectUrl, null, 'stepcheckoutlog.log');
            Mage::log($result, null, 'stepcheckoutlog.log');
           

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
         Mage::log('-----------------------------', null, 'stepcheckoutlog.log');
        
    }
    public function successAction()
    {
        $session = $this->getOnepage()->getCheckout();
        if (!$session->getLastSuccessQuoteId()) {
            $this->_redirect('checkout/cart');
            return;
        }

        $lastQuoteId = $session->getLastQuoteId();
        $lastOrderId = $session->getLastOrderId();
        $lastRecurringProfiles = $session->getLastRecurringProfileIds();
        if (!$lastQuoteId || (!$lastOrderId && empty($lastRecurringProfiles))) {
            $this->_redirect('checkout/cart');
            return;
        }
        if(Mage::getSingleton('core/session')->getGspaysuccess() == '1')
        {
            $order = $this->getOrder();
            $order->sendNewOrderEmail();
        }
        $session->clear();
        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($lastOrderId)));
        $this->renderLayout();
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
