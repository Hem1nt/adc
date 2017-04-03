<?php
class Iksula_Crm_Model_Order_Observer{
    public function insert_in_crm($observer){
        $event_order = $observer->getEvent()->getOrder();
        $order = Mage::getModel('sales/order')->load($event_order->getId());
        $id = $order->getIncrementId();
        $status = $order->getStatus();
        $customer_email = $order->getCustomerEmail();
        $customer_name = $order->getCustomerName();
        $customer_name_lower = strtolower($customer_name);
        //print_r(get_class_methods($order));exit;
        
        $orderData = $order->getData();

        if($order->getData('x_forwarded_for')){
            $ip = $order->getRemoteIp().' ( '.$order->getData('x_forwarded_for').' )';
        }else{
            $ip = $order->getRemoteIp();
        }
        
        $medical_history = array('physicianname'=>$orderData['physicianname'],'physiciantelephone'=>$orderData['physiciantelephone'],'drug_allergies'=>$orderData['drug_allergies'],'current_medications'=>$orderData['current_medications'],'current_treatments'=>$orderData['current_treatments'],'smoke'=>$orderData['smoke'],'drink'=>$orderData['drink']);

        $customerId = $order->getCustomerId();
        $groupId = $order->getCustomerGroupId();
        $gender = $orderData['customer_gender'];
        $callforoffers = $orderData['callforoffers'];
        $timetocall = $orderData['timetocall'];

        
        $order_date = floatval(strtotime($orderData['created_at'])*1000);
        
        $billing_info = $order->getBillingAddress()->getData();
        $shipping_info = $order->getShippingAddress()->getData();
        $billing_name = $billing_info['firstname'].' '.$billing_info['lastname'];

        if($orderData['discount_amount']){
            $discount = $orderData['discount_amount'];
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
            $products[] = array('name'=>$productData['name'],'sku'=>$productData['sku'],'price'=> $productData['price'],'qty'=>$item->getQtyOrdered(),'strength'=>$productData['configurable_attribute']);
        }
        
        $data = array('type'=>'allday', 'orderId'=>$id,'orderStatus'=>$status,'customer_email'=>$customer_email,'customer_name'=>$customer_name,
            'billing'=>array('email'=>$billing_info['email'],'name'=>$billing_name,'telephone'=>$billing_info['telephone'],'street'=>$billing_info['street'],'country'=>$billing_info['country_id'],'state'=>$billing_info['region'],'city'=>$billing_info['city'],'zipcode'=>$billing_info['postcode']),
            'shipping'=>array('telephone'=>$shipping_info['telephone'],'street'=>$shipping_info['street'],'country'=>$shipping_info['country_id'],'state'=>$shipping_info['region'],'city'=>$shipping_info['city'],'zipcode'=>$shipping_info['postcode']),
            'orderAmount'=>array('subtotal'=>$orderData['subtotal'],'shipping'=>$orderData['shipping_amount'],'total'=>$orderData['grand_total'],'tax'=>$orderData['tax_amount'],'discount'=>$discount),
            'order_date'=>$order_date,
            'payment_info'=>array('method'=>$order->getPayment()->getMethodInstance()->getCode(),'title'=>$order->getPayment()->getMethodInstance()->getTitle(),'echeck_id'=>$echeck_transactionid),
            'shipment_info'=>array('method'=>$orderData['shipping_method'],'title'=>$orderData['shipping_description']),
            'invoice'=>array(),'creditmemo'=>array(),'product_details'=>$products,'total_qty'=>$order->getTotalQtyOrdered(),
            'commentHistory'=>$commentHistory,'customer_name_lower'=>$customer_name_lower,
            'remoteIp'=>$ip,'medical_history'=>$medical_history,'groupId'=>$groupId,'customerId'=>$customerId,'gender'=>$gender,'callforoffers'=>$callforoffers,'timetocall'=>$timetocall);
        
        //print_r($data);exit;
        /*Data sent to CRM*/
        $url = 'http://www.alldaychemist.directory/api/orderInsert';
        $ch = curl_init($url);
        $api_post = array('user'=>'admin', 'pass'=>'123456', 'api_key'=>'123er','data'=>urlencode(json_encode($data)));
        foreach($api_post as $key=>$value){ 
            $fields_string .= $key.'='.$value.'&'; 
        }
        rtrim($fields_string, '&');
        curl_setopt($ch, CURLOPT_POST ,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
        echo $Rec_Data = curl_exec($ch); 
        /*insert complete*/

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
        $url = 'http://www.alldaychemist.directory/api/update';
        $ch = curl_init($url);
        $api_post = array('user'=>'admin', 'pass'=>'123456', 'api_key'=>'123er','data'=>urlencode(json_encode($invoice_data)));
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
        $url = 'http://www.alldaychemist.directory/api/update';
        $ch = curl_init($url);
        $api_post = array('user'=>'admin', 'pass'=>'123456', 'api_key'=>'123er','data'=>urlencode(json_encode($creditmemo_data)));
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
        $url = 'http://www.alldaychemist.directory/api/updateStatus';
        $ch = curl_init($url);
        $api_post = array('user'=>'admin', 'pass'=>'123456', 'api_key'=>'123er','data'=>urlencode(json_encode($dataToSend)));
        foreach($api_post as $key=>$value){ 
            $fields_string .= $key.'='.$value.'&'; 
        }

        rtrim($fields_string, '&');
        curl_setopt($ch, CURLOPT_POST ,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
        echo $Rec_Data = curl_exec($ch);
        exit;
    }


    public function insertCustomerCrm(Varien_Event_Observer $observer){
        
        $customer = $observer->getCustomer();
        $customerData = $observer->getCustomer()->getData();
        $created_at = strtotime($customerData['created_at'])*1000;
        $updated_at = strtotime($customerData['updated_at'])*1000;
        $customer_name = $customerData['firstname'].' '.$customer_name['lastname'];
        $name_lower = strtolower($customer_name);
        $data = array('customer_id'=>$customerData['entity_id'],
            'email'=>$customerData['email'],'group_id'=>$customerData['group_id'],'store_id'=>$customerData['store_id'],'created_at'=>$created_at,
        'updated_at' => $updated_at, 'status' => 1, 'firstname' => $customerData['firstname'], 'middlename' => $customerData['middlename'], 'lastname' => $customerData['lastname'], 'name' => $customer_name, 'default_billing' => 'NULL', 'billing_street' => 'NULL', 'billing_postcode' => 'NULL', 'billing_city' => 'NULL', 'billing_telephone' => 'NULL', 'billing_fax' => 'NULL', 'billing_region' => 'NULL', 'billing_country_code' => 'NULL', 'default_shipping' => 'NULL', 'shipping_street' => 'NULL', 'shipping_postcode' => 'NULL', 'shipping_city' => 'NULL', 'shipping_telephone' => 'NULL', 'shipping_fax' => 'NULL', 'shipping_region' => 'NULL', 'shipping_country_code' => 'NULL', 'taxvat' => 'NULL', 'callforoffers' => 'NULL', 'donotcall' => 'NULL', 'timetocall' => 'NULL', 'gender' => $customerData['gender'], 'name_lower' => $name_lower );
        
        /*Data sent to CRM*/
        $url = 'http://www.alldaychemist.directory/api/customerInsert';
        $ch = curl_init($url);
        $api_post = array('user'=>'admin', 'pass'=>'123456', 'api_key'=>'asdfghjkl','data'=>urlencode(json_encode($data)));
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
