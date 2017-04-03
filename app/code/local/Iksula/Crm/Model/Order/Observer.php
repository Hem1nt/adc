<?php
class Iksula_Crm_Model_Order_Observer {

    public $api_key;
    public $api_user;
    public $api_password;
    public $crm_url;

    public function __construct(){
        $this->api_key = Mage::getStoreConfig('crm_api_credentails/crm_api_credentails_details/api_key');
        $this->api_user = Mage::getStoreConfig('crm_api_credentails/crm_api_credentails_details/api_username');
        $this->api_password = Mage::getStoreConfig('crm_api_credentails/crm_api_credentails_details/api_password');
        $this->crm_url = Mage::getStoreConfig('crm_api_credentails/crm_api_credentails_details/api_url');
    }

    public function updateOrderEmail($observer) {
        $data = $observer->getEvent()->getData();
        // $orderData = Mage::getModel('sales/order')->load()->getData();
        $order = Mage::getModel('sales/order')->load($data['data']['orderid']);
        $incrementId = $order->getIncrementId();
        $email = $data['data']['email'];
        $data = array();
        $data['orderId'] = $incrementId;
        $data['email'] = $email;
        /*print_r($data);
        exit;*/

        $url = $this->crm_url.'updateOrderEmail';
        $ch = curl_init($url);
        $api_post = array('user'=>$this->api_user, 'pass'=>$this->api_password, 'api_key'=>$this->api_key, 'data'=>urlencode(json_encode($data)));
        foreach($api_post as $key=>$value){
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string, '&');
        curl_setopt($ch, CURLOPT_POST ,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
        // echo $Rec_Data = curl_exec($ch);
        $Rec_Data = curl_exec($ch);
        //print_r(curl_getinfo($ch));exit;
        // exit;
    }
     public function insertCheckoutCustomer($customerData){


     /* echo "<pre>";
      print_r($customerData);
      exit;*/

       //$customer = $observer->getCustomer();
        //$customerData = $observer->getCustomer()->getData();
        $created_at = strtotime($customerData['created_at'])*1000;
        $updated_at = strtotime($customerData['updated_at'])*1000;
        $dob = strtotime($customerData['customer_dob'])*1000;
        $customer_name = $customerData['customer_firstname'].' '.$customer_name['customer_lastname'];
        $name_lower = strtolower($customer_name);
        $data = array('customer_id'=>$customerData['customer_id'],
            'email'=>$customerData['customer_email'],'group_id'=>$customerData['customer_group_id'],'store_id'=>$customerData['store_id'],'created_at'=>$created_at,
        'updated_at' => $updated_at, 'status' => 1, 'firstname' => $customerData['customer_firstname'], 'middlename' => $customerData['middlename'], 'lastname' => $customerData['lastname'], 'name' => $customer_name, 'default_billing' => 'NULL', 'billing_street' => 'NULL', 'billing_postcode' => 'NULL', 'billing_city' => 'NULL', 'billing_telephone' => 'NULL', 'billing_fax' => 'NULL', 'billing_region' => 'NULL', 'billing_country_code' => 'NULL', 'default_shipping' => 'NULL', 'shipping_street' => 'NULL', 'shipping_postcode' => 'NULL', 'shipping_city' => 'NULL', 'shipping_telephone' => 'NULL', 'shipping_fax' => 'NULL', 'shipping_region' => 'NULL', 'shipping_country_code' => 'NULL', 'taxvat' => 'NULL', 'callforoffers' => 'NULL', 'donotcall' => 'NULL', 'timetocall' => 'NULL','dob'=> $dob, 'gender' => $customerData['customer_gender'], 'name_lower' => $name_lower,'customer_comment'=>'NULL' );

        /*Data sent to CRM*/
        $url = $this->crm_url.'customerInsert';
        $ch = curl_init($url);
        $api_post = array('user'=>$this->api_user, 'pass'=>$this->api_password, 'api_key'=>$this->api_key, 'data'=>urlencode(json_encode($data)));
        foreach($api_post as $key=>$value){
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string, '&');
        curl_setopt($ch, CURLOPT_POST ,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
        $rec_Data = curl_exec($ch);

    }
    public function insertOrders($observer){
        $event_order = $observer->getEvent()->getOrder();
        $order = Mage::getModel('sales/order')->load($event_order->getId());
        $id = $order->getIncrementId();
        $status = $order->getStatus();
        $customer_email = $order->getCustomerEmail();
        $customer_name = $order->getCustomerName();
        $customer_name_lower = strtolower($customer_name);

        $orderFirstName = $order->getCustomerFirstname();
        $orderLastName =  $order->getCustomerLastname();
         $orderLowerFirstName  = strtolower($orderFirstName);
         $orderLowerLastName = strtolower($orderLastName);
        //print_r(get_class_methods($order));exit;
        $quoteId = Mage::getModel('sales/order')->loadByIncrementId($id)->getQuoteId();
        $quote = Mage::getModel('sales/quote')->load($quoteId);
        $method = $quote->getCheckoutMethod(true);
        $customerData = $quote->getData();

        if ($method == 'register'){
           //the customer registered...do your stuff
            //print_r($quote->getData());
            //echo "2424asdasdasd";
            $this->insertCheckoutCustomer($customerData);

        }
        $orderData = $order->getData();
        //print_r(($order->getId()));exit;

        if($order->getData('x_forwarded_for')){
            $ip = $order->getRemoteIp().' ( '.$order->getData('x_forwarded_for').' )';
        }else{
            $ip = $order->getRemoteIp();
        }

        $medical_history = array('physicianname'=>$orderData['physicianname'],'physiciantelephone'=>$orderData['physiciantelephone'],'drug_allergies'=>$orderData['drug_allergies'],'current_medications'=>$orderData['current_medications'],'current_treatments'=>$orderData['current_treatments'],'smoke'=>$orderData['smoke'],'drink'=>$orderData['drink']);

        $customerId = $order->getCustomerId();
        $groupId = $order->getCustomerGroupId();
        if($groupId==0){
            $groupId = '-1';
        }
        $gender = $orderData['customer_gender'];
        $callforoffers = $orderData['callforoffers'];
        $timetocall = $orderData['timetocall'];



        $order_date = floatval(strtotime($orderData['created_at'])*1000);

        $billing_info = $order->getBillingAddress()->getData();
        $shipping_info = $order->getShippingAddress()->getData();
        $billing_name = $billing_info['firstname'].' '.$billing_info['lastname'];
        $shipping_name = $shipping_info['firstname'].' '.$shipping_info['lastname'];

        if($orderData['discount_amount']){
            $discount = $orderData['discount_amount'];
            $coupon_code = $orderData['coupon_code'];
        }else{
            $discount = 0;
        }
        if($orderData['echeck_transactionid']){
            $echeck_transactionid = $orderData['echeck_transactionid'];
        }else{
            $echeck_transactionid = null;
        }

        $commentHistory = array();

        foreach($order->getAllStatusHistory() as $comments){
            $commentH =  array();
            $commentH['comment'] = $comments->getData('comment');
            $commentH['status'] = $comments->getData('status');
            $commentH['created_at'] = strtotime($comments->getData('created_at'))*1000;
            $commentHistory[] = $commentH;
        }

        foreach ($order->getAllItems() as $item) {
            $productData = $item->getProduct()->getData();
            // echo "<pre>"; print_r($productData);
            $price = $productData['price'];
            if(isset($productData['special_price'])) {
                $specialPrice = $productData['special_price'];
                if(!empty($specialPrice)) {
                    $price = $productData['special_price'];
                }
            }
            $products[] = array('name'=>$productData['name'],'sku'=>$productData['sku'],'price'=> $price,'qty'=>$item->getQtyOrdered(),'strength'=>$productData['configurable_attribute']);
        }

        // exit;
        $data = array('type'=>'allday', 'entity_id'=>$order->getId(),'orderId'=>$id, 'orderStatus'=>$status, 'customer_email'=>$customer_email, 'customer_name'=>$customer_name, 'coupon_code'=>$coupon_code,
            'billing'=>array('email'=>$billing_info['email'], 'name'=>$billing_name, 'telephone'=>$billing_info['telephone'], 'street'=>$billing_info['street'], 'country'=>$billing_info['country_id'], 'state'=>$billing_info['region'], 'city'=>$billing_info['city'], 'zipcode'=>$billing_info['postcode']),
            'shipping'=>array('name'=>$shipping_name,'telephone'=>$shipping_info['telephone'],'street'=>$shipping_info['street'],'country'=>$shipping_info['country_id'],'state'=>$shipping_info['region'],'city'=>$shipping_info['city'],'zipcode'=>$shipping_info['postcode']),
            'orderAmount'=>array('subtotal'=>$orderData['subtotal'],'shipping'=>$orderData['shipping_amount'],'total'=>$orderData['grand_total'],'tax'=>$orderData['tax_amount'],'discount'=>$discount),
            'order_date'=>$order_date,
            'payment_info'=>array('method'=>$order->getPayment()->getMethodInstance()->getCode(),'title'=>$order->getPayment()->getMethodInstance()->getTitle(),'echeck_id'=>$echeck_transactionid),
            'shipment_info'=>array('method'=>$orderData['shipping_method'],'title'=>$orderData['shipping_description']),
            'invoice'=>array(),'creditmemo'=>array(),'product_details'=>$products,'total_qty'=>$order->getTotalQtyOrdered(),
            'commentHistory'=>$commentHistory,'customer_name_lower'=>$customer_name_lower,
            'remoteIp'=>$ip,'medical_history'=>$medical_history,'groupId'=>$groupId,'customerId'=>$customerId,'gender'=>$gender,'callforoffers'=>$callforoffers,'timetocall'=>$timetocall,'customer_firstname' =>$orderLowerFirstName,'customer_lastname'=> $orderLowerLastName, 'supply_issue_message' => 'NULL','dispatcher_message'=>'NULL');

        //print_r($data);exit;
        /*Data sent to CRM*/
        $url = $this->crm_url.'orderInsert';
        $ch = curl_init($url);
        $api_post = array('user'=>$this->api_user, 'pass'=>$this->api_password, 'api_key'=>$this->api_key, 'data'=>urlencode(json_encode($data)));
        $fields_string = '';
        foreach($api_post as $key=>$value){
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string, '&');
        curl_setopt($ch, CURLOPT_POST ,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        // echo $Rec_Data = curl_exec($ch);
        $Rec_Data = curl_exec($ch);
        //print_r(curl_getinfo($ch));exit;
        // exit;
        /*insert complete*/

        /*update customers*/
        if($customerId){
            $customer_info = Mage::getModel('customer/customer')->load($customerId);
            $customerData = $customer_info->getData();
            $updated_at = strtotime($customerData['updated_at'])*1000;
            $created_at = strtotime($customerData['created_at'])*1000;
            if(time() != strtotime($customerData['created_at'])) {
                $customer_name = $customerData['firstname'].' '.$customer_name['lastname'];
                $name_lower = strtolower($customer_name);
                // echo strtotime($customerData['created_at'])." ### ".time(); exit;
                if($customer_info->getDefaultBillingAddress()==false) {
                    $billingAddress['street'] = '';
                    $billingAddress['postcode'] = '';
                    $billingAddress['city'] = '';
                    $billingAddress['telephone'] = '';
                    $billingAddress['fax'] = '';
                    $billingAddress['region'] = '';
                    $billingAddress['country_id'] = '';
                } else {
                    $billingAddress = $customer_info->getDefaultBillingAddress()->getData();
                }

                if($customer_info->getDefaultShippingAddress()==false) {
                    $shippingAddress['street'] = '';
                    $shippingAddress['postcode'] = '';
                    $shippingAddress['city'] = '';
                    $shippingAddress['telephone'] = '';
                    $shippingAddress['fax'] = '';
                    $shippingAddress['region'] = '';
                    $shippingAddress['country_id'] = '';
                } else {
                    $shippingAddress = $customer_info->getDefaultShippingAddress()->getData();
                }

                if(isset($customerData['middlename'])){
                    $middlename = $customerData['middlename'];
                }else{
                    $middlename = '';
                }

                $data = array('customer_id'=>$customerData['entity_id'],
                    'email'=>$customerData['email'],'group_id'=>$customerData['group_id'],'store_id'=>$customerData['store_id'],'created_at'=>$created_at,
                'updated_at' => $updated_at, 'status' => 1, 'firstname' => $customerData['firstname'], 'middlename' => $middlename, 'lastname' => $customerData['lastname'], 'name' => $customer_name, 'default_billing' => $customerData['default_billing'], 'billing_street' => $billingAddress['street'], 'billing_postcode' => $billingAddress['postcode'], 'billing_city' => $billingAddress['city'], 'billing_telephone' => $billingAddress['telephone'], 'billing_fax' => $billingAddress['fax'], 'billing_region' => $billingAddress['region'], 'billing_country_code' => $billingAddress['country_id'], 'default_shipping' => $customerData['default_shipping'], 'shipping_street' => $shippingAddress['street'], 'shipping_postcode' => $shippingAddress['postcode'], 'shipping_city' => $shippingAddress['city'], 'shipping_telephone' => $shippingAddress['telephone'], 'shipping_fax' => $shippingAddress['fax'], 'shipping_region' => $shippingAddress['region'], 'shipping_country_code' => $shippingAddress['country_id'], 'taxvat' => 'NULL', 'callforoffers' => $customerData['callforoffers'], 'donotcall' => $customerData['donotcall'], 'timetocall' => $customerData['timetocall'], 'gender' => $customerData['gender'], 'name_lower' => $name_lower );

                /*echo '<pre>';
                print_r($data);exit;*/

                /*Data sent to CRM*/
                $url = $this->crm_url.'updateCustomers';
                //echo $url."<br/>";exit;
                $ch = curl_init($url);
                $api_post = array('user'=> $this->api_user, 'pass'=> $this->api_password,
                    'api_key'=> $this->api_key, 'data'=>urlencode(json_encode($data)));
                /*print_r($api_post);
                exit;*/
                foreach($api_post as $key=>$value){
                    $fields_string .= $key.'='.$value.'&';
                }
                rtrim($fields_string, '&');
                curl_setopt($ch, CURLOPT_POST ,1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
                curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
                curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
                $rec_Data = curl_exec($ch);
                /*insert complete*/
            }
        }

    }

    public function insert_after_invoice_generate(Varien_Event_Observer $observer){
        // echo 'insert_after_invoice_generate'; exit;
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        $order_data = $invoice->getData();

        $invoice_data = Array();

        $invoice_data['orderId'] = $order_id = $order->getIncrementId(); //order Id
        $invoice_data['invoiceId'] = $invoice_id = $order_data['increment_id']; // Increment Id
        $invoice_data['inv_created_at'] = $invoice_created_at = $order_data['created_at'];
        $invoice_data['inv_grand_total'] = $grand_total = $order_data['grand_total'];
        /*Data sent to CRM*/
        $url = $this->crm_url.'update';
        $ch = curl_init($url);
        $api_post = array('user'=>$this->api_user, 'pass'=>$this->api_password, 'api_key'=>$this->api_key, 'data'=>urlencode(json_encode($invoice_data)));
        foreach($api_post as $key=>$value){
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string, '&');
        curl_setopt($ch, CURLOPT_POST ,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
        //echo $Rec_Data = curl_exec($ch);
        /*insert complete*/
        //exit;
        // echo 'insert_after_customer_create'; exit;
    }

    public function insert_after_creditmemo_generate(Varien_Event_Observer $observer){
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order_id = $creditmemo->getOrder()->getIncrementId();
        $creditmemo_info = $creditmemo->getData();

        $creditmemo_data = Array();
        $creditmemo_data['orderId'] = $order_id;
        $creditmemo_data['creditmemoId'] = $creditmemo_info['increment_id'];
        $creditmemo_data['credit_created_at'] = $creditmemo_info['created_at'];
        $creditmemo_data['credit_grand_total'] = $creditmemo_info['grand_total'];
        /*Data sent to CRM*/
        $url = $this->crm_url.'update';
        $ch = curl_init($url);
        $api_post = array('user'=>$this->api_user, 'pass'=>$this->api_password, 'api_key'=>$this->api_key, 'data'=>urlencode(json_encode($creditmemo_data)));
        foreach($api_post as $key=>$value){
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string, '&');
        curl_setopt($ch, CURLOPT_POST ,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
        //echo $Rec_Data = curl_exec($ch);
        /*insert complete*/
        //exit;
        // echo 'insert_after_customer_create'; exit;
    }



    public function insert_after_shipment_create(Varien_Event_Observer $observer){
        $creditmemo = $observer->getEvent()->getShipment()->getData();
        echo '<pre/>';

         $shipment_info = array();
         // if ($order->hasShipments()) {
         //     foreach ($order->getShipmentsCollection() as $shipment) {
         //         $shipment_info = $shipment->getData();
         //         $tracking_info = array();
         //         foreach($shipment->getAllTracks() as $track){
         //           $tracks = array();
         //           $tracks['track_number'] = $track->getData('track_number');
         //           $tracks['title'] = $track->getData('title');
         //           $tracks['created_at'] = strtotime($track->getData('created_at'))*1000;
         //           $tracking_info[] = $tracks;
         //         }
         //      }
         // }
       /* print_r($creditmemo);
        exit;*/
    }

    public function updateStatus(Varien_Event_Observer $observer){

        $data = $observer->getEvent()->getData();
        foreach($data['data'] as $id){
            $order = Mage::getModel('sales/order')->load($id);
            $orderData = $order->getData();
            $commentHistory = array();
            foreach($order->getAllStatusHistory() as $comments){
                $commentH =  array();
                $commentH['comment'] = $comments->getData('comment');
                $commentH['status'] = $comments->getData('status');
                $commentH['created_at'] = floatval(strtotime($comments->getData('created_at'))*1000);
                $commentHistory[] = $commentH;
            }

            $dataToSend[] = array('orderId'=>$orderData['increment_id'],'commentHistory'=>$commentHistory,'orderStatus'=>$orderData['status']);
        }
        /*echo '<pre>';
        print_r($dataToSend);
        exit;*/

        /*Data sent to CRM*/
        $url = $this->crm_url.'updateStatus';
        $ch = curl_init($url);
        $api_post = array('user'=>$this->api_user, 'pass'=>$this->api_password, 'api_key'=>$this->api_key, 'data'=>urlencode(json_encode($dataToSend)));
        foreach($api_post as $key=>$value){
            $fields_string .= $key.'='.$value.'&';
        }

        rtrim($fields_string, '&');
        curl_setopt($ch, CURLOPT_POST ,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
        $Rec_Data = curl_exec($ch);
    }


    public function insertCustomerCrm(Varien_Event_Observer $observer){
        // echo "OLo"; exit;
        $customer = $observer->getCustomer();
        $customerData = $observer->getCustomer()->getData();
        $created_at = strtotime($customerData['created_at'])*1000;
        $updated_at = strtotime($customerData['updated_at'])*1000;
        $dob = strtotime($customerData['dob'])*1000;
        $customer_name = $customerData['firstname'].' '.$customer_name['lastname'];
        $name_lower = strtolower($customer_name);
        $data = array('customer_id'=>$customerData['entity_id'],
            'email'=>$customerData['email'],'group_id'=>$customerData['group_id'],'store_id'=>$customerData['store_id'],'created_at'=>$created_at,
        'updated_at' => $updated_at, 'status' => 1, 'firstname' => $customerData['firstname'], 'middlename' => $customerData['middlename'], 'lastname' => $customerData['lastname'], 'name' => $customer_name, 'default_billing' => 'NULL', 'billing_street' => 'NULL', 'billing_postcode' => 'NULL', 'billing_city' => 'NULL', 'billing_telephone' => 'NULL', 'billing_fax' => 'NULL', 'billing_region' => 'NULL', 'billing_country_code' => 'NULL', 'default_shipping' => 'NULL', 'shipping_street' => 'NULL', 'shipping_postcode' => 'NULL', 'shipping_city' => 'NULL', 'shipping_telephone' => 'NULL', 'shipping_fax' => 'NULL', 'shipping_region' => 'NULL', 'shipping_country_code' => 'NULL', 'taxvat' => 'NULL', 'callforoffers' => 'NULL', 'donotcall' => 'NULL', 'timetocall' => 'NULL','dob'=>$dob,'gender' => $customerData['gender'],'name_lower' => $name_lower,'customer_comment'=>'NULL' );

        /*Data sent to CRM*/
        /*echo "<pre>";
        print_r($data);
        exit;*/
        $url = $this->crm_url.'customerInsert';
        $ch = curl_init($url);
        $api_post = array('user'=>$this->api_user, 'pass'=>$this->api_password, 'api_key'=>$this->api_key, 'data'=>urlencode(json_encode($data)));
        foreach($api_post as $key=>$value){
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string, '&');
        curl_setopt($ch, CURLOPT_POST ,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
        $rec_Data = curl_exec($ch);
        /*insert complete*/
    }

    public function updateCustomer(Varien_Event_Observer $observer){
        // echo "Hello"; exit;
        $customer = $observer->getCustomer();
        $customerData = $customer->getData();
        $updated_at = strtotime($customerData['updated_at'])*1000;
        $created_at = strtotime($customerData['created_at'])*1000;
         $dob = strtotime($customerData['dob'])*1000;
        if(time() != strtotime($customerData['created_at'])) {
            $customer_name = '';
            $customer_name .= $customerData['firstname'].' '.$customer_name['lastname'];
            $name_lower = strtolower($customer_name);
            // echo strtotime($customerData['created_at'])." ### ".time(); exit;
            if($customer->getDefaultBillingAddress()==false) {
                $billingAddress['street'] = '';
                $billingAddress['postcode'] = '';
                $billingAddress['city'] = '';
                $billingAddress['telephone'] = '';
                $billingAddress['fax'] = '';
                $billingAddress['region'] = '';
                $billingAddress['country_id'] = '';
            } else {
                $billingAddress = $customer->getDefaultBillingAddress()->getData();
            }

            if($customer->getDefaultShippingAddress()==false) {
                $shippingAddress['street'] = '';
                $shippingAddress['postcode'] = '';
                $shippingAddress['city'] = '';
                $shippingAddress['telephone'] = '';
                $shippingAddress['fax'] = '';
                $shippingAddress['region'] = '';
                $shippingAddress['country_id'] = '';
            } else {
                $shippingAddress = $customer->getDefaultShippingAddress()->getData();
            }
            if(isset($customerData['middlename'])){
                $middlename = $customerData['middlename'];
            }else{
                $middlename = '';
            }
            $data = array('customer_id'=>$customerData['entity_id'],
                'email'=>$customerData['email'],'group_id'=>$customerData['group_id'],'store_id'=>$customerData['store_id'],'created_at'=>$created_at,'dob'=>$dob,
            'updated_at' => $updated_at, 'status' => 1, 'firstname' => $customerData['firstname'], 'middlename' => $middlename, 'lastname' => $customerData['lastname'], 'name' => $customer_name, 'default_billing' => $customerData['default_billing'], 'billing_street' => $billingAddress['street'], 'billing_postcode' => $billingAddress['postcode'], 'billing_city' => $billingAddress['city'], 'billing_telephone' => $billingAddress['telephone'], 'billing_fax' => $billingAddress['fax'], 'billing_region' => $billingAddress['region'], 'billing_country_code' => $billingAddress['country_id'], 'default_shipping' => $customerData['default_shipping'], 'shipping_street' => $shippingAddress['street'], 'shipping_postcode' => $shippingAddress['postcode'], 'shipping_city' => $shippingAddress['city'], 'shipping_telephone' => $shippingAddress['telephone'], 'shipping_fax' => $shippingAddress['fax'], 'shipping_region' => $shippingAddress['region'], 'shipping_country_code' => $shippingAddress['country_id'], 'taxvat' => 'NULL', 'callforoffers' => $customerData['callforoffers'], 'donotcall' => $customerData['donotcall'], 'timetocall' => $customerData['timetocall'], 'gender' => $customerData['gender'], 'name_lower' => $name_lower,'customer_comment'=>$customerData['customer_comment'] );

            /*echo '<pre>';
            print_r($data);exit;*/

            /*Data sent to CRM*/
            $url = $this->crm_url.'updateCustomers';
            // echo $url."<br/>";
            $ch = curl_init($url);
            $api_post = array('user'=> $this->api_user, 'pass'=> $this->api_password,
                'api_key'=> $this->api_key, 'data'=>urlencode(json_encode($data)));
            /*print_r($api_post);
            exit;*/
            $fields_string = '';
            foreach($api_post as $key=>$value){
                $fields_string .= $key.'='.$value.'&';
            }
            rtrim($fields_string, '&');
            curl_setopt($ch, CURLOPT_POST ,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
            curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
            curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
            $rec_Data = curl_exec($ch);
            // exit;
            /*insert complete*/
        }
    }

    public function updateOrderAddress(Varien_Event_Observer $observer){
        $data = $observer->getEvent()->getData();

        $data['data']['street'] = implode(' ', $data['data']['street']);
        $regionModel = Mage::getModel('directory/region')->load($data['data']['region_id']);
        $data['data']['region'] = $regionModel->getName();
        /*remove unset others expect middlename in morning */
        unset($data['data']['form_key']);
        unset($data['data']['prefix']);
        unset($data['data']['firstname']);
        unset($data['data']['lastname']);
        unset($data['data']['suffix']);
        unset($data['data']['company']);
        unset($data['data']['fax']);
        unset($data['data']['vat_id']);
        unset($data['data']['middlename']);
        $dataToSend[] = array('orderId'=>$data['orderId'],'data'=>$data['data'],'type'=>$data['type']);

        /*print_r($data['data']);exit;*/

        /*Data sent to CRM*/
        $url = $this->crm_url.'updateAddress';
        $ch = curl_init($url);
        $api_post = array('user'=>$this->api_user, 'pass'=>$this->api_password, 'api_key'=>$this->api_key, 'data'=>urlencode(json_encode($dataToSend)));
        foreach($api_post as $key=>$value){
            $fields_string .= $key.'='.$value.'&';
        }

        rtrim($fields_string, '&');
        curl_setopt($ch, CURLOPT_POST ,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
        $Rec_Data = curl_exec($ch);
        /*print_r($Rec_Data);exit;*/
    }
    public function shippTracking(Varien_Event_Observer $observer){
        $data = $observer->getEvent()->getData();

        $returnData = $this->sendToCrm($data, $this->crm_url."updateTrackingNumber");


    }
    public function addNewFaq(Varien_Event_Observer $observer){
        $data = $observer->getEvent()->getData();
         /*echo "<pre>";
         echo "test";
        print_r($data);
        exit;*/
        $returnData = $this->sendToCrm($data, $this->crm_url."addNewFaq");
        // echo $this->crm_url."addNewFaq"; exit;

    }
    public function updateEcheckId(Varien_Event_Observer $observer){
        $data = $observer->getEvent()->getData();

        $returnData = $this->sendToCrm($data, $this->crm_url."updateEcheckId");


    }
     public function adminCustomerInsert($observer){
        //$data = $observer->getEvent()->getCustomer();
        $customerData = $observer->getEvent()->getCustomer()->getData();
        $created_at = strtotime($customerData['created_at'])*1000;
        $updated_at = strtotime($customerData['updated_at'])*1000;
        $dob = strtotime($customerData['dob'])*1000;
        $customer_name = $customerData['firstname'].' '.$customer_name['lastname'];
        $name_lower = strtolower($customer_name);
        $data = array('customer_id'=>$customerData['entity_id'],
            'email'=>$customerData['email'],'group_id'=>$customerData['group_id'],'store_id'=>$customerData['store_id'],'created_at'=>$created_at,
        'updated_at' => $updated_at, 'status' => 1, 'firstname' => $customerData['firstname'], 'middlename' => $customerData['middlename'], 'lastname' => $customerData['lastname'], 'name' => $customer_name, 'default_billing' => 'NULL', 'billing_street' => 'NULL', 'billing_postcode' => 'NULL', 'billing_city' => 'NULL', 'billing_telephone' => 'NULL', 'billing_fax' => 'NULL', 'billing_region' => 'NULL', 'billing_country_code' => 'NULL', 'default_shipping' => 'NULL', 'shipping_street' => 'NULL', 'shipping_postcode' => 'NULL', 'shipping_city' => 'NULL', 'shipping_telephone' => 'NULL', 'shipping_fax' => 'NULL', 'shipping_region' => 'NULL', 'shipping_country_code' => 'NULL', 'taxvat' => 'NULL', 'callforoffers' => 'NULL', 'donotcall' => 'NULL', 'timetocall' => 'NULL','dob'=>$dob,'gender' => $customerData['gender'],'name_lower' => $name_lower,'customer_comment'=>$customerData['customer_comment'] );

        /*Data sent to CRM*/
        /*echo "<pre>";
        print_r($data);
        exit;*/
        $url = $this->crm_url.'customerInsert';
        $ch = curl_init($url);
        $api_post = array('user'=>$this->api_user, 'pass'=>$this->api_password, 'api_key'=>$this->api_key, 'data'=>urlencode(json_encode($data)));
        foreach($api_post as $key=>$value){
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string, '&');
        curl_setopt($ch, CURLOPT_POST ,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
        $rec_Data = curl_exec($ch);
    }

    public function sendToCrm($dataToSend, $url) {
        // $url = $this->crm_url.'updateAddress';
        $ch = curl_init($url);
        $api_post = array('user'=>$this->api_user, 'pass'=>$this->api_password, 'api_key'=>$this->api_key, 'data'=>urlencode(json_encode($dataToSend)));
        foreach($api_post as $key=>$value){
            $fields_string .= $key.'='.$value.'&';
        }

        rtrim($fields_string, '&');
        curl_setopt($ch, CURLOPT_POST ,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
        $Rec_Data = curl_exec($ch);
        return $Rec_Data;
    }
    public function orderCancel($observer){
       $order = $observer->getPayment()->getOrder();
        $data = $order->getData();


            $order = Mage::getModel('sales/order')->load($data['entity_id']);
            $orderData = $order->getData();
            $commentHistory = array();
            foreach($order->getAllStatusHistory() as $comments){
                $commentH =  array();
                $commentH['comment'] = $comments->getData('comment');
                $commentH['status'] = $comments->getData('status');
                $commentH['created_at'] = floatval(strtotime($comments->getData('created_at'))*1000);
                $commentHistory[] = $commentH;
            }
            $cancelStatus[] = array('comment'=>'','status'=>'Canceled','created_at'=>time()*1000);
            //print_r($test);
            //echo '<pre>';
          $comments = array_merge($cancelStatus,$commentHistory);
            //
            $dataToSend[] = array('orderId'=>$orderData['increment_id'],'commentHistory'=>$comments,'orderStatus'=>$orderData['status']);
        //print_r($dataToSend);
        //exit;


        /*Data sent to CRM*/
        $url = $this->crm_url.'updateStatus';
        $ch = curl_init($url);
        $api_post = array('user'=>$this->api_user, 'pass'=>$this->api_password, 'api_key'=>$this->api_key, 'data'=>urlencode(json_encode($dataToSend)));
        foreach($api_post as $key=>$value){
            $fields_string .= $key.'='.$value.'&';
        }

        rtrim($fields_string, '&');
        curl_setopt($ch, CURLOPT_POST ,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
        $Rec_Data = curl_exec($ch);
    }

    public function updateSupplyIssue($observer){
        $data = $observer->getEvent()->getData();
        //echo "<pre>";print_r($data);
        //exit;
        //$id =
        $order = Mage::getModel('sales/order')->load($data['data']['entity_id']);
         $incrementId = $order->getIncrementId();
         $message = trim($data['data']['supply_issue_message']);
         $dataToSend = array('order_id'=>$incrementId,'supply_issue_message'=>$message);
         //print_r($dataToSend);
         //exit;
         $url = $this->crm_url.'updateSupplyIssue';
        $ch = curl_init($url);
        $api_post = array('user'=>$this->api_user, 'pass'=>$this->api_password, 'api_key'=>$this->api_key, 'data'=>urlencode(json_encode($dataToSend)));
        foreach($api_post as $key=>$value){
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string, '&');
        curl_setopt($ch, CURLOPT_POST ,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
       /* echo $Rec_Data = curl_exec($ch);
        exit; */
        $Rec_Data = curl_exec($ch);
    }
    public function updateDispatcher($observer){
        $data = $observer->getEvent()->getData();
        //echo "<pre>";print_r($data);
        //exit;
        //$id =
        $order = Mage::getModel('sales/order')->load($data['data']['entity_id']);
         $incrementId = $order->getIncrementId();
         $message = trim($data['data']['dispatcher_message']);
         $dataToSend = array('order_id'=>$incrementId,'dispatcher_message'=>$message);
         //print_r($dataToSend);
         //exit;
         $url = $this->crm_url.'updateDispatcher';
        $ch = curl_init($url);
        $api_post = array('user'=>$this->api_user, 'pass'=>$this->api_password, 'api_key'=>$this->api_key, 'data'=>urlencode(json_encode($dataToSend)));
        foreach($api_post as $key=>$value){
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string, '&');
        curl_setopt($ch, CURLOPT_POST ,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
       /* echo $Rec_Data = curl_exec($ch);
        exit; */
        $Rec_Data = curl_exec($ch);
    }

     public function updatePaymentinfo($observer){
        $data = $observer->getEvent()->getData();
        //$id =
        $order = Mage::getModel('sales/order')->load($data['data']['entity_id']);
         $incrementId = $order->getIncrementId();
         $message = trim($data['data']['paymentinfo_message']);
         $dataToSend = array('order_id'=>$incrementId,'paymentinfo_message'=>$message);

         //print_r($dataToSend);
         //exit;
         $url = $this->crm_url.'updatePaymentinfo';
        $ch = curl_init($url);
        $api_post = array('user'=>$this->api_user, 'pass'=>$this->api_password, 'api_key'=>$this->api_key, 'data'=>urlencode(json_encode($dataToSend)));
        foreach($api_post as $key=>$value){
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string, '&');
        curl_setopt($ch, CURLOPT_POST ,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
       /* echo $Rec_Data = curl_exec($ch);
        exit; */
        $Rec_Data = curl_exec($ch);
    }

    public function updateTodate($observer){
        $data = $observer->getEvent()->getData();
        //echo "<pre>";print_r($data);
        //exit;
        //$id =
        $order = Mage::getModel('sales/order')->load($data['data']['entity_id']);
         $incrementId = $order->getIncrementId();
         $message = trim($data['data']['todate_message']);
         $dataToSend = array('order_id'=>$incrementId,'todate_message'=>$message);
         //print_r($dataToSend);
         //exit;
         $url = $this->crm_url.'updateTodate';
        $ch = curl_init($url);
        $api_post = array('user'=>$this->api_user, 'pass'=>$this->api_password, 'api_key'=>$this->api_key, 'data'=>urlencode(json_encode($dataToSend)));
        foreach($api_post as $key=>$value){
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string, '&');
        curl_setopt($ch, CURLOPT_POST ,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
       /* echo $Rec_Data = curl_exec($ch);
        exit; */
        $Rec_Data = curl_exec($ch);
    }

    public function updateFromdate($observer){
        $data = $observer->getEvent()->getData();
        //echo "<pre>";print_r($data);
        //exit;
        //$id =
        $order = Mage::getModel('sales/order')->load($data['data']['entity_id']);
         $incrementId = $order->getIncrementId();
         $message = trim($data['data']['fromdate_message']);
         $dataToSend = array('order_id'=>$incrementId,'fromdate_message'=>$message);
         //print_r($dataToSend);
         //exit;
         $url = $this->crm_url.'updateFromdate';
        $ch = curl_init($url);
        $api_post = array('user'=>$this->api_user, 'pass'=>$this->api_password, 'api_key'=>$this->api_key, 'data'=>urlencode(json_encode($dataToSend)));
        foreach($api_post as $key=>$value){
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string, '&');
        curl_setopt($ch, CURLOPT_POST ,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
       /* echo $Rec_Data = curl_exec($ch);
        exit; */
        $Rec_Data = curl_exec($ch);
    }

}
