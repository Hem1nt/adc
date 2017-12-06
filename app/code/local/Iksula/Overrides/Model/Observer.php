<?php
class Iksula_Overrides_Model_Observer
{

    public function adminhtmlWidgetContainerHtmlBefore($event)
    {
        $block = $event->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View) {
            // $message = Mage::helper('overrides')->__('Are you sure you want to do this?');
            $block->addButton('print_shipment_button', array(
                'label'     => Mage::helper('overrides')->__('Print Shipment'),
                'onclick'   => "setLocation('{$block->getUrl('*/sales_order/print_single_shipment')}')",
                'class'     => 'go'
                ));
        }
    }

    public function removeCookie($observer)
    {
        $loginAsCustomer = Mage::getModel('core/cookie')->get('login_admin');
        if($loginAsCustomer)
        {
            Mage::getModel('core/cookie')->delete('login_admin');
        }
    }
    public function countrySession($observer)
    {
        $userIpAddress = Mage::helper('core/http')->getRemoteAddr();
        // $observer->getEvent()->getControllerAction()->getFullActionName();
        $geopluginURL='http://www.geoplugin.net/php.gp?ip='.$userIpAddress;
        $addrDetailsArr = unserialize(file_get_contents($geopluginURL)); 
        $countryCode = $addrDetailsArr['geoplugin_countryCode'];
        Mage::getSingleton('core/session')->unsUserCountryCode();
        Mage::getSingleton('core/session')->setUserCountryCode($countryCode);

    }

    public function productRedirects($observer)
    {
    $action = $observer->getControllerAction();
     $request = $observer->getEvent()->getControllerAction()->getRequest();
     $controllerName = $request->getControllerName();
     $actionName = $request->getActionName();
     $customUrl = $controllerName.'/'.$actionName;
     if($customUrl != 'method/productReview'){
        Mage::getSingleton('core/cookie')->delete('productCookie');
     }
     $requestUrl = rtrim($request->getScheme() . '://' . $request->getHttpHost().$_SERVER['REQUEST_URI']);
     if(Mage::getStoreConfig('productredirect/general/enable')==1){      
        if(Mage::getStoreConfig('productredirect/general/upload') AND file_exists(Mage::getBaseDir('media') . '/productwise/redirects/' . Mage::getStoreConfig('productredirect/general/upload')))
        {
            $redirectLines = file(Mage::getBaseDir('media') . '/productwise/redirects/' . Mage::getStoreConfig('productredirect/general/upload'));
            array_shift($redirectLines);
            foreach ($redirectLines AS $redirectLine){
              $sourceDestination = explode(',', $redirectLine);
              $sourceUrl = rtrim(trim($sourceDestination[0]), '/');
              if(count($sourceDestination) == 2){
                $destinationUrl = trim($sourceDestination[1]);
                $redirectCode = trim($sourceDestination[2]);

                if($sourceUrl == $requestUrl){
                    $response = Mage::app()->getResponse();
                    $response->setRedirect($destinationUrl, 301);
                    $response->sendResponse();
                    exit;
                }
                continue;
            }
        }
    }
}
}

public function catalog_product_save_commit_after($observer)
{
 $_product =  $observer->getProduct();
 $_product->cleanCache();
 $conf = Mage::getModel('catalog/product_type_configurable')->setProduct($_product);
 $col = $conf->getUsedProductCollection()->addAttributeToSelect('*')->addFilterByRequiredOptions();
 foreach($col as $simple_product){
    $simple_product->setStockData(array('is_in_stock' => $_product->getStockItem()->getIsInStock()));
    $simple_product->save();
}
}
public function insertStockMovement(Mage_CatalogInventory_Model_Stock_Item $stockItem, $message = '')
{
         // print_r($stockItem->getIsInStock());exit();
    if ($stockItem->getId()) {
        $product = Mage::getModel('catalog/product')->load($stockItem->getProductId());
        $product->cleanCache();
        $conf = Mage::getModel('catalog/product_type_configurable')->setProduct($product);
        $col = $conf->getUsedProductCollection()->addAttributeToSelect('*')->addFilterByRequiredOptions();
        foreach($col as $simple_product){
            $simple_product->setStockData(array('is_in_stock' => $stockItem->getIsInStock()));
            $simple_product->save();
        }

    }
}

public function saveStockItemAfter($observer)
{
    $stockItem = $observer->getEvent()->getItem();
    if (!$stockItem->getStockStatusChangedAutomaticallyFlag() || $stockItem->getOriginalInventoryQty() != $stockItem->getQty()) {
        if (!$message = $stockItem->getSaveMovementMessage()) {
            if (Mage::getSingleton('api/session')->getSessionId()) {
                $message = 'Stock saved from Magento API';
            } else {
                $message = 'Stock saved manually';
            }
        }
        $this->insertStockMovement($stockItem, $message);
    }
}

protected function _getUserId()
{
    $userId = null;
    if (Mage::getSingleton('customer/session')->isLoggedIn()) {
        $userId = Mage::getSingleton('customer/session')->getCustomerId();
    } elseif (Mage::getSingleton('admin/session')->isLoggedIn()) {
        $userId = Mage::getSingleton('admin/session')->getUser()->getId();
    }

    return $userId;
}

protected function _getUsername()
{
    $username = '-';
    if (Mage::getSingleton('api/session')->isLoggedIn()) {
        $username = Mage::getSingleton('api/session')->getUser()->getUsername();
    } elseif (Mage::getSingleton('customer/session')->isLoggedIn()) {
        $username = Mage::getSingleton('customer/session')->getCustomer()->getName();
    } elseif (Mage::getSingleton('admin/session')->isLoggedIn()) {
        $username = Mage::getSingleton('admin/session')->getUser()->getUsername();
    }

    return $username;
}

public function cartsavebefore()
{
    $session = Mage::getSingleton('checkout/session');
    $bundled_product = new Mage_Catalog_Model_Product();

    foreach ($session->getQuote()->getAllItems() as $item) {
        if($item->getProductType()=='bundle'){
            $item_id_cart = $item->getId();
            $product_id_cart = $item->getProductId();
            $bundled_product->load($product_id_cart);
            $selectionCollection = $bundled_product->getTypeInstance(true)->getSelectionsCollection(
                $bundled_product->getTypeInstance(true)->getOptionsIds($bundled_product), $bundled_product
                );
            $bundled_items = array();

            foreach($selectionCollection as $option)
            {
                $bundled_items[] = $option->product_id;
            }

            foreach ($session->getQuote()->getAllItems() as $childitem) {
                if(in_array($childitem->getProductId(),$bundled_items)){
                    $childitem->setParentItemId($item_id_cart)->save();
                }
            }
        }
    }
}

    // public function paymentMethodIsActive(Varien_Event_Observer $observer) {}
public function paymentMethodIsActive(Varien_Event_Observer $observer) {
    $event           = $observer->getEvent();
    $method          = $event->getMethodInstance();
    $result          = $event->getResult();
        // echo $method->getCode().'<br>';
    $isFrontend = Mage::getDesign()->getArea();
        // exit;

    if($isFrontend=='frontend'){
        $customerLoggedIn = Mage::getSingleton('customer/session')->isLoggedIn();
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $customerEmail = $quote->getCustomerEmail();
            // $orderCount = $this->orderCount($customerEmail);

            // echo $orderCount;
        $billingCountry = $quote->getBillingAddress()->getCountry();
        $customerData = Mage::getSingleton('customer/session')->getCustomer();
            $customer_id = $customerData->getId(); // set this to the ID of the customer.

            if($customerLoggedIn){            

                $customer_data = Mage::getModel('customer/customer')->load($customer_id);
                $configValue = Mage::getStoreConfig('payment/pay/registration_date');
                $customerCreatedDate = Mage::getStoreConfig('payment/pay/customer_created_date');
                $newCustomerCreatedDate = Mage::getStoreConfig('payment/pay/new_customer_created_date');
                $limitDateNewCustomerCreatedDate = date('d-m-Y', strtotime($newCustomerCreatedDate));
                $limitDate = date('d-m-Y', strtotime($configValue));
                $customerDate = date('d-m-Y', strtotime($customer_data->getData('created_at')));
                if(strtotime($limitDate) > strtotime($customerDate))
                {
                    $oldCustomerPayments = $this->oldCustomerPayments();
                    if(in_array($method->getCode(),$oldCustomerPayments)){
                        $result->isAvailable = true;  

                        /*if($method->getCode()=='echeckpayment'){ 
                            if($billingCountry=='US'){
                                $result->isAvailable = true; 
                            }else{
                                $result->isAvailable = false; 
                            }
                        }*/

                        // if($method->getCode()=='esafepayment'){
                        //     if($billingCountry=='US'){
                        //         if($orderCount >= 2){
                        //             $result->isAvailable = true;
                        //         }else{
                        //             $result->isAvailable = false; 
                        //         }
                        //     }else{
                        //          $result->isAvailable = false; 
                        //     }
                        // }

                    }
                    else{
                        $result->isAvailable = false;
                    }

                }
                else{
                    $newCustomerPayments = $this->newCustomerPayments();
                    if(in_array($method->getCode(),$newCustomerPayments)) {                        
                        // if($method->getCode()=='echeckapi'){
                        //     if($orderCount >= 1){
                        //         $result->isAvailable = true;
                        //     }else{
                        //         $result->isAvailable = false; 
                        //     }
                        // }

                        /**/

                        // if($method->getCode()=='pay'){
                        // //     if($billingCountry!='US'){
                        //     $payOrderCount = $this->payOrderCount($customerEmail);
                        //     if($payOrderCount>=1){
                        //         $result->isAvailable = true;
                        //     }else{
                        //         $result->isAvailable = false;
                        //     }
                        // //     }else{
                        // //         $result->isAvailable = false;
                        // //     }    
                        // }

                       /* if($method->getCode()=='otherpayments'){
                            if($billingCountry=='US'){
                                if(strtotime($limitDateNewCustomerCreatedDate) > strtotime($customerDate)){
                                        $result->isAvailable = false;
                                }else{
                                     $result->isAvailable = true; 
                                }
                            }elseif ($billingCountry=='GB' || $billingCountry=='AU') {
                                $result->isAvailable = false;
                            }
                            else{
                                $result->isAvailable = true;
                            } 
                        }*/


                        //     if($orderCount < 1){
                        //         $result->isAvailable = true;
                        //     }else{
                        //         $result->isAvailable = false; 
                        //     }
                        // }

                        /*if($method->getCode()=='echeckpayment'){ 
                            if($billingCountry=='US'){
                                $result->isAvailable = true; 
                            }else{
                                $result->isAvailable = false; 
                            }
                        }*/

                        // if($method->getCode()=='esafepayment'){
                        //     if($billingCountry=='US'){
                        //         if($orderCount >= 2){
                        //             $result->isAvailable = true;
                        //         }else{
                        //             $result->isAvailable = false; 
                        //         }
                        //     }else{
                        //          $result->isAvailable = false; 
                        //     }
                        // }

                    }
                    else{
                        $result->isAvailable = false;
                    }
                }
            }else{
                $guestCustomerPayments = $this->guestCustomerPayments();
                if(in_array($method->getCode(),$guestCustomerPayments)) {

                    /*if($method->getCode()=='echeckpayment'){
                        if($billingCountry=='US'){
                            $result->isAvailable = true; 
                        }else{
                            $result->isAvailable = false; 
                        }
                    }*/

                    // if($method->getCode()=='pay'){
                    //     if($billingCountry=='US' || $billingCountry=='GB' || $billingCountry=='AU'){
                    //         $result->isAvailable = true;
                    //     }else{
                    //         $result->isAvailable = false;
                    //     }    
                    // }

                    // if($method->getCode()=='otherpayments'){
                    //     if($billingCountry=='US' || $billingCountry=='GB' || $billingCountry=='AU'){
                    //         $result->isAvailable = false;
                    //     }else{
                    //         $result->isAvailable = true;
                    //     }    
                    // }

                    /*if($method->getCode()=='otherpayments'){
                        if($billingCountry!='US'){
                            $result->isAvailable = true;
                        }else{
                            $result->isAvailable = false;
                        }    
                    }*/


                    // if($method->getCode() == 'echeckapi'){
                    //     if($orderCount >= 1){
                    //         $result->isAvailable = true;
                    //     }else{
                    //         $result->isAvailable = false; 
                    //     }
                    // }

                    // if($method->getCode()=='pay'){
                    //         // if($billingCountry!='US'){
                    //     $payOrderCount = $this->payOrderCount($customerEmail);
                    //     if($payOrderCount>=1){
                    //         $result->isAvailable = true;
                    //     }else{
                    //         $result->isAvailable = false;
                    //     }
                    //         // }else{
                    //             // $result->isAvailable = false;
                    //         // }    
                    // }

                    // if($method->getCode()=='esafepayment'){
                    //     if($billingCountry=='US'){
                    //         if($orderCount >= 2){
                    //             $result->isAvailable = true;
                    //         }else{
                    //             $result->isAvailable = false; 
                    //         }
                    //     }else{
                    //          $result->isAvailable = false; 
                    //     }
                    // }
                }
                else{
                    $result->isAvailable = false;
                }
            }

            /* Start Payment Restriction for Countries and Ips */
            $ipRestrictionEnable = Mage::getStoreConfig('payment_ip_restriction/general/ip_enable');
            if($ipRestrictionEnable==1){
                $userIpAddress = Mage::helper('core/http')->getRemoteAddr();
                $userCountryByIp = Mage::getSingleton('core/session')->getUserCountryCode();
                $ipRestriction = Mage::getStoreConfig('payment_ip_restriction/general/countries');
                $ipRestrictionCountry = explode(',', $ipRestriction);

                $whitelist_ip_address = Mage::getStoreConfig('payment_ip_restriction/general/whitelist_ip');
                $whitelist_ips = $this->_ipTextToArray($whitelist_ip_address);

                if(in_array($userCountryByIp, $ipRestrictionCountry)){
                    if(!in_array($userIpAddress, $whitelist_ips)){
                        $result->isAvailable = false; 
                    }
                }
            }            
            /* End of Payment Restriction for Countries and Ips */

            /*start We dont ship to your country*/
            $shippingCountry = $quote->getShippingAddress()->getCountry();

                // $billingCountry = $quote->getBillingAddress()->getCountry();
            $allowCountries = explode(',', (string)Mage::getStoreConfig('general/country/allow'));
            if(!in_array($shippingCountry,$allowCountries)){
                $result->isAvailable = false;
            }
            /*end of We dont ship to your country*/

        }
    }
    public function showAllPayments(Varien_Event_Observer $observer){
        $event           = $observer->getEvent();
        $method          = $event->getMethodInstance();
        $result          = $event->getResult();
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $billingCountry = $quote->getBillingAddress()->getCountry();
        if(Mage::getSingleton('customer/session')->isLoggedIn())
          {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $customerID = $customer->getEntityId();
          }
        $cookieAdminUser = Mage::getModel('core/cookie')->get('login_admin');
        $isFrontend = Mage::getDesign()->getArea();
        if($isFrontend =='frontend' && $cookieAdminUser){
          /*Client can see all payment methods*/
            $allPaymentMethod = $this->allPayments();
            if($cookieAdminUser){
                  if ($cookieAdminUser == $customerID && in_array($method->getCode(),$allPaymentMethod)) {
                      $result->isAvailable = true;
                  } else {
                      $result->getActiveMethods = true;
                  }
              }
              /*Hiding DRC From US Customers S*/ //review this code . no use here - amit mourya
              // if(!$cookieAdminUser && $method->getCode()== 'drc'){
              //   if($billingCountry == 'US')
              //     {
              //       $result->isAvailable = false;
              //     }else{
              //       $result->isAvailable = true;
              //     }
              // }
              /*Hiding DRC From US Customers E*/
          }

    }
    public function allPayments(){
       $allPaymentMethod = Mage::getStoreConfig('payment/allpayment/allpayment_method');
       $methods = array_filter(explode(",",$allPaymentMethod));
       return $methods;
    }

    protected function _ipTextToArray($text)
    {
        $ips = preg_split("/[\n\r]+/", $text);
        foreach ($ips as $ipsk => $ipsv) {
            if (trim($ipsv) == "") {
                unset($ips[$ipsk]);
            }
        }
        return $ips;
    }

    public function oldCustomerPayments(){
     $oldcustomerpayment = Mage::getStoreConfig('payment/pay/oldcustomerpayment');
     $methods = array_filter(explode(",",$oldcustomerpayment));
     return $methods;

 }

   /**
   * List of Payment method visible for New Customer
   * @return : active payment methods for old customers
   */
   public function newCustomerPayments(){
     $newcustomerpayment = Mage::getStoreConfig('payment/pay/newcustomerpayment');
     $methods = array_filter(explode(",",$newcustomerpayment));
     return $methods;
 }

   /**
   * List of Payment method visible for Guest Customer
   * @return : active payment methods for old customers
   */
   public function guestCustomerPayments(){
     $guestcustomerpayment = Mage::getStoreConfig('payment/pay/guestcustomerpayment');
     $methods = array_filter(explode(",",$guestcustomerpayment));
     return $methods;
 }

 public function lastOrderDate($email_address){

    $orders = Mage::getResourceModel('sales/order_collection')
    ->addFieldToSelect('*')
    ->addFieldToFilter('customer_email',$email_address)
    ->addAttributeToSort('created_at', 'DESC')
    ->setPageSize(1);

    $customerCreatedDate = Mage::getStoreConfig('payment/pay/customer_created_date');
    if($orders->getSize()==1){
        $createdAt = $orders->getFirstItem()->getCreatedAt();
        if(strtotime($customerCreatedDate) > strtotime($createdAt)){
            return 1;
        }else{
            return 0;
        }
    }else{
        return 0;
    }


}

public function orderCount($email){
      // $status = 'complete';
      // $status = Mage::getStoreConfig('order_status/general/selected_status');
      // $orderData = Mage::getStoreConfig('order_status/general/order_date');
      // $statusArray = explode(',',$status);

      // $createdDate = date('Y-m-d', strtotime(Mage::getStoreConfig('payment/pay/customer_date')));

  $order = $this->orderCollectionData($email);
  $count = $order->getSize();
  return $count;        
}

public function payOrderCount($email){
      // $status = 'complete';
  $status = Mage::getStoreConfig('order_status/general/selected_status');
  $orderData = Mage::getStoreConfig('order_status/general/order_date');
  $statusArray = explode(',',$status);

      // $createdDate = date('Y-m-d', strtotime(Mage::getStoreConfig('payment/pay/customer_date')));

      // $order = $this->orderCollectionData($email);
  $order = Mage::getModel('sales/order')->getCollection();
  $order->addFieldToFilter('customer_email',$email);
  $order->addFieldToFilter('status',array('in' => $statusArray));
  $order->addFieldToFilter('created_at',array('lteq' => $orderData));
  $count = $order->getSize();
  return $count;        
}

public function orderCollectionData($email)
{
    $status = Mage::getStoreConfig('order_status/general/selected_status');
    $orderData = Mage::getStoreConfig('order_status/general/order_date');
    $statusArray = explode(',',$status);

    $order = Mage::getModel('sales/order')->getCollection();
    $order->addFieldToFilter('customer_email',$email);
    $order->addFieldToFilter('status',array('in' => $statusArray));

    return $order;

}

    // // apply referal coupon for reffered customer for first order else unset coupon
    // public function salesruleValidatorProcess(Varien_Event_Observer $observer) {
    //     $ruleId = Mage::getStoreConfig('custom_snippet/referral_id/referral_rule');
    //     $quote = $observer->getEvent()->getQuote();

    //     $ruleCollection = Mage::getModel('salesrule/rule');
    //     // $item = $observer->getEvent()->getItem();
    //     // $rule = $observer->getEvent()->getRule();

    //     $rewardpoints = Mage::getModel('rewardpoints/referral')->getCollection()
    //     ->addFieldToFilter('rewardpoints_referral_email',$quote->getData('customer_email'));

    //     if($rewardpoints) {
    //         foreach ($rewardpoints as $rewardpoint) {
    //             $rewardpoints_referral_parent_id[] = $rewardpoint->getData('rewardpoints_referral_parent_id');
    //         }

    //         $rewardpoints_referral_parent_id = implode(" ",$rewardpoints_referral_parent_id);
    //     }

    //     // Coupon can also be used by guest customer
    //     // Mage::getSingleton('customer/session')->isLoggedIn() == '1' &&

    //     if($rewardpoints_referral_parent_id != '') {
    //     $orders = Mage::getResourceModel('sales/order_collection') ->addFieldToSelect('*') ->addFieldToFilter('customer_id', $quote->getData('customer_id'));

    //        // if previous orders set discount amount to zero
    //         if($orders->getSize()) {
    //             $rule = $ruleCollection->load($ruleId);
    //             $rule->setDiscountAmount(0);
    //             $rule->save();
    //         }else {
    //             // referral discount coupon is applied
    //             $rule = $ruleCollection->load($ruleId);
    //             $rule->setDiscountAmount(10);
    //             $rule->save();
    //         }
    //     }else {
    //         // set referral discount to zero

    //             $rule = $ruleCollection->load($ruleId);
    //             $rule->setDiscountAmount(0);
    //             $rule->save();
    //     }
    // }

public function guesttoregister(){

    $fromdate = date('Y-m-d H:i:s',strtotime('-1 day', time()));
    $todate = date('Y-m-d H:i:s');

    $collection = Mage::getModel('customer/customer')->getCollection()
    ->addAttributeToSelect('*')
    ->addFieldToFilter('created_at', array(
        'from'     => $fromdate,
        'to'       => $todate,
        ));

    foreach ($collection as $item)
    {
        $customerId = $item->getData('entity_id');
        $customer_email = $item->getData('email');
        $customer_firstname = $item->getData('firstname');
        $customer_lastname = $item->getData('lastname');

        $orderCollection = Mage::getModel('sales/order')->getCollection();
        $orderCollection->addFieldToFilter('customer_email', $customer_email);
        foreach ($orderCollection as $_order)
        {
            $customer_id = $_order->getData('customer_id');
                // $customer_email = $_order->getData('customer_email');
            $increment_id = $_order->getData('increment_id');
            if($customer_id == '')
            {
                $SalesCollection = Mage::getModel('sales/order');
                $orderbyid = $SalesCollection->loadByIncrementId($increment_id);
                $orderbyid->setCustomerId($customerId);
                $orderbyid->setCustomerFirstname($customer_firstname);
                $orderbyid->setCustomerLastname($customer_lastname);
                $orderbyid->setCustomerEmail($customer_email);
                $orderbyid->save();

                $billingAddress = $orderbyid->getBillingAddress();
                $shippingAddress = $orderbyid->getShippingAddress();
                    // mapping of address
                $this->addressmap($billingAddress,$shippingAddress,$customerId);
            }

        }

    }

}

public function addressmap($billAddress,$shipAddress,$customerId) {

    $billingdata = array(
        'firstname'=>$billAddress->getData('firstname'),
        'middlename'=>$billAddress->getData('middlename'),
        'lastname'=>$billAddress->getData('lastname'),
        'company'=>$billAddress->getData('company'),
        'telephone'=>$billAddress->getData('telephone'),
        'fax'=>$billAddress->getData('fax'),
        'street'=>$billAddress->getData('street'),
        'city'=>$billAddress->getData('city'),
        'region'=>$billAddress->getData('region'),
        'region_id'=>$billAddress->getData('region_id'),
        'postcode'=>$billAddress->getData('postcode'),
        'country_id'=>$billAddress->getData('country_id'),
        );

    $defaultBilling = 1;
    $customAddress = Mage::getModel('customer/address');

    $customAddress->setData($billingdata)
    ->setCustomerId($customerId)
    ->setIsDefaultBilling($defaultBilling)
    ->setSaveInAddressBook('1');
    $customAddress->save();

    $shippingdata = array(
        'firstname'=>$shipAddress->getData('firstname'),
        'middlename'=>$shipAddress->getData('middlename'),
        'lastname'=>$shipAddress->getData('lastname'),
        'company'=>$shipAddress->getData('company'),
        'telephone'=>$shipAddress->getData('telephone'),
        'fax'=>$shipAddress->getData('fax'),
        'street'=>$shipAddress->getData('street'),
        'city'=>$shipAddress->getData('city'),
        'region'=>$shipAddress->getData('region'),
        'region_id'=>$shipAddress->getData('region_id'),
        'postcode'=>$shipAddress->getData('postcode'),
        'country_id'=>$shipAddress->getData('country_id'),
        );

    $defaultShipping = 1;
    $customAddress->setData($shippingdata)
    ->setCustomerId($customerId)
    ->setIsDefaultShipping($defaultShipping)
    ->setSaveInAddressBook('1');
    $customAddress->save();
}

public function deleteQuote(Varien_Event_Observer $observer){
    $order_id = $observer->getData('order_ids');
    $order = Mage::getModel('sales/order')->load($order_id[0]);
    $quoteId = $order->getData('quote_id');
    $status = $order->getData('status');
    $storeId = $order->getData('store_id');
    $store = Mage::getModel('core/store')->load($storeId);
    $quote = Mage::getModel('sales/quote')->setStore($store)->load($quoteId);
    $quote->delete()->save();
}

public function getIpOfCustomer(Varien_Event_Observer $observer){
    $event = $observer->getEvent();
    $customer = $event->getCustomer();
    $remoteAddr = Mage::helper('core/http')->getRemoteAddr(); 
    $customerId = $customer->getData('entity_id');
    $storeId = Mage::app()->getStore()->getStoreId();
    $store = Mage::getModel('core/store')->load($storeId);
    $customerData = Mage::getModel('customer/customer')->setStore($store)->load($customerId);
    $customerData->setRegisterIp($remoteAddr)->save();
}

public function removePaymentMethod($observer){
    $event = $observer->getEvent();
    $quote = Mage::getSingleton("checkout/cart")->getQuote();
    $quote->getPayment()->setMethod();
    $quote->setTotalsCollectedFlag(false)->collectTotals();
    $quote->save();
        // exit;
        // echo $quote->getPayment()->getMethodInstance()->getCode();exit
}

public function deleteExpiredCoupon($observer){
    $quote = $observer->getEvent()->getQuote();
    $coupon = $quote->getCouponCode();
    $currentTimestamp = Mage::getModel('core/date')->timestamp(time());
    $currentDate = date('Y-m-d h:i:s', $currentTimestamp);

    $couponCode = explode("-", $coupon);
    if($couponCode[0] == 'HB'){
        $birthdayModel = Mage::getModel('birthday/birthday')->getCollection()
        ->addFieldToFilter('coupon',$coupon);
        $item = $birthdayModel->getFirstItem()->getData();
        $createdDate =  $item['coupon_created_date'];
        $expiredDate =  $item['coupon_expire_date'];
        if($createdDate > $currentDate || $expiredDate < $currentDate){
            $quote->setCouponCode('')->save();
                // $quote->collectTotals()->save();
        }
    }    
}

public function changegroup($observer){
        // $order_id = $observer->getEvent()->getOrderIds();
        // $incrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        // $order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
        // $regularGroupId = 6;
        // $generalGroupId = 1;
        // $customerEmail = $order->getCustomerEmail();
        // $customerId = $order->getCustomerId();
        // $orderCollection = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_email',$customerEmail);
        // $orderHelper = Mage::helper('frontend/order');
        // $customersOrdersCount = $orderHelper->getCustomersOrdersCount($customerEmail);
        // if($customerId){
        //     if($customersOrdersCount >= 2){
        //         foreach ($orderCollection as $_order) {
        //             $_order->setCustomerGroupId($regularGroupId);
        //             $_order->save();
        //         }

        //         $_customer = Mage::getModel('customer/customer')->load($customerId);
        //         $_customer->setGroupId($regularGroupId);
        //         $_customer->save();

        //     }elseif($customersOrdersCount == 1){
        //         foreach ($orderCollection as $_order) {
        //             $_order->setCustomerGroupId($generalGroupId);
        //             $_order->save();
        //         }

        //         $_customer = Mage::getModel('customer/customer')->load($customerId);
        //         $_customer->setGroupId($generalGroupId);
        //         $_customer->save(); 

        //     }
        // }else{
        //     if($customersOrdersCount == 1){
        //         foreach ($orderCollection as $_order) {
        //             $_order->setCustomerGroupId($generalGroupId);
        //             $_order->save();
        //         }

        //     }elseif($customersOrdersCount >= 2){
        //         foreach ($orderCollection as $_order) {
        //             $_order->setCustomerGroupId($regularGroupId);
        //             $_order->save();
        //         } 

        //     }
        // }


}

public function customerOrderGroupChange($observer){
    $event = $observer->getEvent();
    $ids = $event->getOrderIds();
    $incrementId = $ids[0];
    $order = Mage::getModel('sales/order')->load($incrementId);
    $regularGroupId = 6;
    $premiumGroupId = 2;
    $newGroupId = 8;
    $guestGroupId = 9;
    $customerId = $order->getCustomerId();
    $customerEmail = $order->getCustomerEmail();
    $orderCollection = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_email',$customerEmail);
    $orderHelper = Mage::helper('frontend/order');
    $customersOrdersCount = $orderHelper->getCustomersOrdersCount($customerEmail);
    //$customersOrdersCount = count($orderCollection->getData());
    if($customerId){
        if($customersOrdersCount >= 3){
            foreach ($orderCollection as $_order) {
                $_order->setCustomerGroupId($premiumGroupId);
                $_order->save();
            }

            $_customer = Mage::getModel('customer/customer')->load($customerId);
            $_customer->setGroupId($premiumGroupId);
            $_customer->save();

        }elseif($customersOrdersCount >= 1 && $customersOrdersCount <= 2){
            foreach ($orderCollection as $_order) {
                $_order->setCustomerGroupId($regularGroupId);
                $_order->save();
            }

            $_customer = Mage::getModel('customer/customer')->load($customerId);
            $_customer->setGroupId($regularGroupId);
            $_customer->save(); 

        }elseif($customersOrdersCount == 0){
            foreach ($orderCollection as $_order) {
                $_order->setCustomerGroupId($newGroupId);
                $_order->save();
            }

            $_customer = Mage::getModel('customer/customer')->load($customerId);
            $_customer->setGroupId($newGroupId);
            $_customer->save(); 
        }
    }else{
        if($customersOrdersCount >= 3){
            foreach ($orderCollection as $_order) {
                $_order->setCustomerGroupId($premiumGroupId);
                $_order->save();
            }
        }elseif($customersOrdersCount >= 1 && $customersOrdersCount <= 2){
            foreach ($orderCollection as $_order) {
                $_order->setCustomerGroupId($regularGroupId);
                $_order->save();
            }
        }elseif($customersOrdersCount == 0){
            foreach ($orderCollection as $_order) {
                $_order->setCustomerGroupId($guestGroupId);
                $_order->save();
            }
        }
    }
}

public function reviewStatusChange($observer){
    $review = $observer->getDataObject();
    $productId = $review->getEntityPkValue();
    $customerId = $review->getCustomerId();
    $salesOrderCollection = Mage::getModel('sales/order')->getCollection()->addFieldtoFilter('customer_id',$customerId);
    if ($review->hasDataChanges()) {
        $newStatus = $review->getData('status_id');
        if ($newStatus != Mage_Review_Model_Review::STATUS_APPROVED) {
            foreach ($salesOrderCollection as $order) {
                foreach ($order->getAllVisibleItems() as $item) {
                    $id = $this->getParentId($item);
                    if($id == $productId){
                        $item->setIsReviwed(0)->save();
                    }
                }
            }
        }
    }
}

public function getParentId($item)  {
    if($item->getProductType() != 'bundle'){
        $parentId = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($item->getProductId());
        return $parentId[0];
    }else{
        $product = Mage::getModel('catalog/product')->load($item->getProductId());
        return $product->getId();
    }
}

}
