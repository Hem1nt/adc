<?php
class Iksula_Crm_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
        
        $url = 'http://54.209.227.141/api';
        $ch = curl_init($url);
        $api_post = array('user'=>'admin', 'pass'=>'123456', 'api_key'=>'asdfghjkl');
        foreach($api_post as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');
        curl_setopt($ch, CURLOPT_POST ,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
        echo $Rec_Data = curl_exec($ch);
        
        /*$this->loadLayout();   
        $this->getLayout()->getBlock("head")->setTitle($this->__("Titlename"));
        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
        "label" => $this->__("Home Page"),
        "title" => $this->__("Home Page"),
        "link"  => Mage::getBaseUrl()
        ));

        $breadcrumbs->addCrumb("titlename", array(
        "label" => $this->__("Titlename"),
        "title" => $this->__("Titlename")
        ));

        $this->renderLayout(); 
        */	  
    }
    public function InsertOrdersingleAction(){
        $orderArray = json_decode(urldecode($_POST['data']), true);
        foreach($orderArray as $orders=>$order) {
            $orderObj = Mage::getModel('sales/order')->loadByIncrementId($order['orderId']);
            //print_r($orderObj->getData());exit;
            if($orderObj->getData()) {
                if(isset($order['commentDetails'])) {
                    $updateComment = $order['commentDetails']['comment'];
                } else {
                    $updateComment = "";
                }

                $mainComment = $order['originalComment'];

                //echo $order['statusCode']."##".$order['statusTitle'];
                $orderObj->setData('state', $order['statusTitle']);
                $orderObj->setStatus($order['statusCode']);
                $updateComment = str_replace("<span class='commentBy'>","", $updateComment);
                $updateComment = str_replace("</span>","", $updateComment);
                $history = $orderObj->addStatusHistoryComment($updateComment, false);
                $history->setIsCustomerNotified(false);
                // $orderObj->sendOrderUpdateEmail(true, $updateComment);

                //$orderObj->sendOrderUpdateEmail(true, $mainComment);
                /*order mail*/
                
                /*order mail*/
                
                try {
                    $orderObj->save();
                    $returnData['success'][] = $order['orderId'];
                } catch (Exception $e) {
                    $returnData['error'][] = $order['orderId'];
                }
                // echo "<pre>"; print_r($orderArray); exit;
            } else {
                $returnData['delete'][] = $order['orderId'];
            }

        }
        //echo "###<pre>"; print_r($returnData); exit;
        echo json_encode($returnData); exit;
        // echo "Hello";
    }


    public function InsertOrderAction(){
        $orderArray = json_decode(urldecode($_POST['data']), true);
        foreach($orderArray as $orders=>$order) {
            $orderObj = Mage::getModel('sales/order')->loadByIncrementId($order['orderId']);
            //print_r($orderObj->getData());exit;
            if($orderObj->getData()) {
                if(isset($order['commentDetails'])) {
                    $updateComment = $order['commentDetails']['comment'];
                } else {
                    $updateComment = "";
                }

                $mainComment = $order['originalComment'];

                //echo $order['statusCode']."##".$order['statusTitle'];
                $orderObj->setData('state', $order['statusTitle']);
                $orderObj->setStatus($order['statusCode']);
                $updateComment = str_replace("<span class='commentBy'>","", $updateComment);
                $updateComment = str_replace("</span>","", $updateComment);
                $history = $orderObj->addStatusHistoryComment($updateComment, false);
                $history->setIsCustomerNotified(true);
                // $orderObj->sendOrderUpdateEmail(true, $updateComment);

                //$orderObj->sendOrderUpdateEmail(true, $mainComment);
                /*order mail*/
                switch($order['statusCode']){                        
                    case "Money Order/Check Received":   
                        $templateId = 1;
                        break;                         
                    case "canceled": 
                        $templateId = 3;
                        break;                         
                    case "Refund Issued":   
                        $templateId = 4;
                        break;  
                    case "Order Shipped":   
                        $templateId = 6;
                        break;
                    case "On Hold": 
                        $templateId = 7;
                        break;
                    case "Shipped With Post":   
                        $templateId = 8;
                        break;
                    case "Incomplete Payment Process":  
                        $templateId = 9;
                        break;
                    case "awaiting_check_transfer": 
                        $templateId = 10;
                        break;
                    case "dispensing":  
                        $templateId = 11;
                        break;
                    case "Dispensing":  
                        $templateId = 11;
                        break;
                    case "Shipped With Registerd Mail": 
                        $templateId = 16;
                        break;
                    case "Shipped With tracking Number":    
                        $templateId = 17;
                        break;
                    case "eCheck Payment Accepted": 
                        $templateId = 18;
                        break;
                    case "Pending eCheck Payment":  
                         $templateId = 19;
                         break;
                    case "eCheck Payment Declined": 
                        $templateId = 20;
                        break;
                    case "Awaiting eCheck Payment": 
                        $templateId = 21;
                        break;
                    case "payment_accepted":    
                        $templateId = 23;
                        break;
                    case "transaction_declined":    
                        $templateId = 24;
                        break;
                    case "transaction_declined_vt": 
                        $templateId = 30;
                        break;
                    case "pendingecheck":   
                        $templateId = 26;
                        break;
                    case "want_to_pay": 
                        $templateId = 27;
                        break;
                    case "voice_message_left":  
                        $templateId = 28;
                        break;
                    case "payment_accepted_vt":
                        $templateId = 29;
                        break;
                    case "pay_by_echeck":
                        $templateId = 35;
                        break;
                    case "no_answer_response":  
                        $templateId = 999;
                        break;
                    case "donotcall":
                        $templateId = 999;
                        break;
                    case "complete":
                        $templateId = 999;
                        break;
                    case "closed":
                        $templateId = 999;
                        break;
                    case "supply_issue":
                        $templateId = 34;
                        break;
                    case "order_fulfilled":
                        $templateId = 999;
                        break;
                    case "pay_now":
                        $templateId = 36;
                        break;
                    default:
                        $templateId = 15;
                }
                
                $tempID = $templateId;
                $mailTemplate = Mage::getModel('core/email_template');
                $translate  = Mage::getSingleton('core/translate');
                
                $template_collection =  $mailTemplate->load($templateId);                                
                $template_data = $template_collection->getData();
                if(!empty($template_data)){
                    $templateId = $template_data['template_id'];
                    $mailSubject = $template_data['template_subject'];                                                
                    //fetch sender data from Adminend > System > Configuration > Store Email Addresses > General Contact
                    $from_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email
                    $from_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name
             
                    $sender = array('name'  => $from_name,'email' => $from_email);                                 
                    $customer['cust_name'] =  $orderObj->getBillingAddress()->getName();
                    $customer['order_id']= $orderObj->getData('increment_id');
                    $customer['order_entity_id']= $orderObj->getId();
                    $customer['amount']= sprintf("%0.2f", $orderObj->getGrandTotal());
                    $customer['staus_label'] = $order['statusCode'];
                    $customer['customer_email'] = $orderObj->getData("customer_email");
                    $customer['email'] = $orderObj->getData("email");
                    $customer['street'] = $orderObj->getShippingAddress()->getData('street');
                    $customer['region'] = $orderObj->getShippingAddress()->getData('region');
                    $customer['city'] = $orderObj->getShippingAddress()->getData('city');
                    $customer['postcode'] = $orderObj->getShippingAddress()->getData('postcode');
                    $country_code = $orderObj->getShippingAddress()->getData('country_id');
                    $billing_country_code = $orderObj->getBillingAddress()->getData('country_id');
                    $customer['country'] = Mage::app()->getLocale()->getCountryTranslation($country_code);
                    $customer['bi_country'] = Mage::app()->getLocale()->getCountryTranslation($billing_country_code);
                    $customer['link'] = 'https://www.alldaychemist.in/xgsifpsjkjjspfhd/?order_id='.base64_encode($customer['order_entity_id']);
                    //$customer['customer_email'] = $order->getData("email");
                    
                    //add code for get tracking number
                    if($tempID == '17' || $tempID == '16'){
                        $shipment_collection = Mage::getResourceModel('sales/order_shipment_collection');
                        $shipment_collection->addAttributeToFilter('order_id', $order->getId());
                        $track_no = "";
                        $date = "";
                        foreach($shipment_collection as $sc) {
                            $shipment = Mage::getModel('sales/order_shipment');
                            $shipment->load($sc->getId());
                            foreach ($shipment->getAllTracks() as $o_item){
                                if($track_no == ""){
                                    $track_no = $o_item->getNumber();
                                    $date = date("M d", strtotime(Mage::helper('core')->formatDate($o_item->getAssignDate().' 12:12:12', 'medium', 'true')));
                                }else{
                                    $track_no = $track_no.", ".$o_item->getNumber();
                                    $date = $date." and ".date("M d", strtotime(Mage::helper('core')->formatDate($o_item->getAssignDate().' 12:12:12', 'medium', 'true')));
                                }
                                $year = date("Y", strtotime(Mage::helper('core')->formatDate($o_item->getAssignDate().' 12:12:12', 'medium', 'true')));
                            }
                        
                        }
                    }
                    $customer['track_no'] = $track_no;
                    $customer['date'] = $date;
                    $customer['year'] = $year;
                    $vars = $customer; //for replacing the variables in email with data 
                    $storeId = 1;   /*This is optional*/                    
                    $model = $mailTemplate->setReplyTo($sender['email'])->setTemplateSubject($mailSubject);
                    $email = $vars['customer_email'];
                    $name = $vars['cust_name'];                                        
                    $status = $vars['staus_label'];                                        
                    $model->sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);                     
                    if (!$mailTemplate->getSentSuccess()) {
                            throw new Exception();
                    }
                    $translate->setTranslateInline(true);
                }
                /*order mail*/
                
                try {
                    $orderObj->save();
                    $returnData['success'][] = $order['orderId'];
                } catch (Exception $e) {
                    $returnData['error'][] = $order['orderId'];
                }
                // echo "<pre>"; print_r($orderArray); exit;
            } else {
                $returnData['delete'][] = $order['orderId'];
            }

        }
        //echo "###<pre>"; print_r($returnData); exit;
        echo json_encode($returnData); exit;
        // echo "Hello";
    }

    public function updateCustomerAction(){
        //echo 'Magento to CRM';
        $data = json_decode(urldecode($_POST['data']), true);
        $customerEmail = $data['customerEmail'];
        $customerData = $data['customerData'];
        //print_r($data['customerData'][0]);exit;
        $customer = Mage::getModel('customer/customer')->loadByEmail($customerEmail);
        if($customer){
            try {
                $addressId = $customer->getDefaultBilling();
                $shippingAddressId = $customer->getDefaultShipping();
                if ($addressId){
                    $address = Mage::getModel('customer/address')->load($addressId);
                    $address->setCity($data['customerData'][0]['billing_city']);
                    $address->setRegion($data['customerData'][0]['billing_region']);
                    $address->setPostcode($data['customerData'][0]['billing_postcode']);
                    $address->setTelephone($data['customerData'][0]['billing_telephone']);
                    $address->setStreet($data['customerData'][0]['billing_street']);
                    $address->save();
                }
                if($shippingAddressId){
                    $address = Mage::getModel('customer/address')->load($shippingAddressId);
                    $address->setCity($data['customerData'][0]['shipping_city']);
                    $address->setRegion($data['customerData'][0]['shipping_region']);
                    $address->setPostcode($data['customerData'][0]['shipping_postcode']);
                    $address->setTelephone($data['customerData'][0]['shipping_telephone']);
                    $address->setStreet($data['customerData'][0]['shipping_street']);
                    $address->save();
                }
                $returnData['success'][] = $customer->getEmail();
            }catch(Exception $e){
                $returnData['error'][] = $customer->getEmail();
            }
        }else{
            $returnData['delete'][] = $customer->getEmail();
        }
        echo json_encode($returnData); exit;        
    }

    public function updateCustomerEmailAction() {

        $data = json_decode(urldecode($this->getRequest()->getParam('data')),true);
        $orderIds = $data['orderIds'];
        
        $newEmail = $data['newEmail'];
        $oldEmail = $data['oldEmail'];
        $customer = Mage::getModel('customer/customer')->loadByEmail($oldEmail);

        if($customer){
            try{
                $customer->setEmail($newEmail)->save();
                /*print_r($customer->getData());*/
                foreach($orderIds as $orderId) {
                    $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
                    $orderData = $order->getData();
                    if(!empty($orderData)) {
                        $order->getBillingAddress()->setData('email', $newEmail);
                        $order->setCustomerEmail($newEmail)->save();                       
                    }
                }                
                $returnData['success'][] = $customer->getEmail();
            }
            catch(Exception $e){
                $returnData['error'][] = $customer->getEmail();
            }    
        }else{
            $returnData['delete'][] = $customer->getEmail();   
        }
        echo json_encode($returnData);exit;       
    }

    public function updateBillingAddressAction() {
        $data = $this->getRequest()->getParam('data');
        $data = json_decode($data, true);
        $orderId = $data['order_id'];
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        $billingAddress = $order->getBillingAddress();
        $addressId = $billingAddress->getId();
        $address = Mage::getModel('sales/order_address')->load($addressId);
        // echo "<pre>"; print_r($data);
        // echo "<pre>";
        $name = explode(' ', $data['name']);
        // print_r($order->getBillingAddress()->getData());
        if($order) {
            try {
                if($addressId) {
                    $address->setFirstname($name[0]);
                    $address->setLastname($name[1]);
                    $address->setStreet($data['street']);
                    $address->setCity($data['city']);
                    $address->setPostcode($data['zipcode']);
                    $address->setRegion($data['state']['name']);
                    if($data['state']['code']){
                        $address->setRegionId($data['state']['code']);
                    }else{
                        $address->setRegionId('');
                    }
                    $address->setTelephone($data['telephone']);
                    $address->setCountryId($data['country']);
                    $address->save();
                }
                $returnData['success'][] = $orderId;
            } catch(Exception $e) {
                $returnData['error'][] = $orderId;
            }
        } else {
            $returnData['delete'][] = $orderId;
        }
        /*print_r($order->getBillingAddress()->getData());exit;*/
        echo json_encode($returnData); exit;        
    }
    
    public function updateShippingAddressAction() {
        // echo "hello"; exit;
        $data = $this->getRequest()->getParam('data');
        $data = json_decode($data, true);
        // echo "<pre>"; print_r($data); exit;
        $orderId = $data['order_id'];
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        $shippingAddress = $order->getShippingAddress();
        $addressId = $shippingAddress->getId();
        $address = Mage::getModel('sales/order_address')->load($addressId);
        /*echo "<pre>"; print_r($data); 
        print_r($order->getBillingAddress()->getData());*/
        $name = explode(' ', $data['name']);
        if($order){
            try{
                if($addressId){
                    $address->setFirstname($name[0]);
                    $address->setLastname($name[1]);
                    $address->setStreet($data['street']);
                    $address->setCity($data['city']);
                    $address->setPostcode($data['zipcode']);
                    $address->setRegion($data['state']['name']);
                    if($data['state']['code']){
                        $address->setRegionId($data['state']['code']);
                    }else{
                        $address->setRegionId('');
                    }
                    $address->setTelephone($data['telephone']);
                    $address->setCountryId($data['country']);
                    $address->save();                    
                }
                $returnData['success'][] = $orderId;
            }catch(Exception $e){
                $returnData['error'][] = $orderId;
            }
        }else{
            $returnData['delete'][] = $orderId;
        }
        
        echo json_encode($returnData); exit;     
    }

    public function getCountryDataAction() {
        $countryList = Mage::getResourceModel('directory/country_collection')
                    ->loadData()
                    ->toOptionArray(false);
        foreach($countryList as $key=>$country) {
            $countryMod = Mage::getModel('directory/country')->load($country['value']); //get country name
            $countryName = $countryMod->getName(); //get country name
            $countryId = $countryMod->getId(); //get country name
            $data[$countryId]['name'] = $countryName;

            $states = Mage::getModel('directory/country')->load($country['value'])->getRegions();

            foreach ($states as $state) {       
                $data[$countryId]['state'][$state->getId()] = $state->getName();
            }
        }
        echo json_encode($data);
    }

    public function updateCustomerPasswordAction(){
        $data = json_decode($this->getRequest()->getParam('data'),true);
        $customer = Mage::getModel('customer/customer')->loadByEmail($data['customerEmail']);
        $returnData = array();
        if($customer){
            try {
                $customer->setPassword($data['password'])->save();
                $customer->sendPasswordReminderEmail();
                $returnData['success'][] = $customer->getEmail();
            } catch (Exception $e) {
                $returnData['error'][] = $customer->getEmail();
            }
        }else{
           $returnData['delete'][] = $customer->getEmail();
        }   
        echo json_encode($returnData);
    }
    public function updateSingleOrderEmailAction(){
        $data = json_decode($this->getRequest()->getParam('data'),true);
        //echo "<pre>"; print_r($data); 
        $orderId = $data['order_id'];
        $customerEmail = $data['customer_email'];
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        if($order){
            try {
                $order->setCustomerEmail($customerEmail)->save();
                //echo "<pre>"; print_r($order->getData());exit;
                $returnData['success'][] = $orderId;
            } catch(Exception $e) {
                $returnData['error'][] = $orderId;
            }
        } else {
            $returnData['delete'][] = $orderId;            
        }
        echo json_encode($returnData);exit;
    }
    public function saveFaqAction() {
        $data = json_decode(urldecode($this->getRequest()->getParam('data')),true);
        $backendfaqModel = Mage::getModel('backendfaq/backendfaq');
        try {
            $data = array('question' => $data['q'],
            'answer' => $data['a'],
            'date' => date('Y-m-d', $data['date']/1000),
            'username' => $data['username'],
            'topic' => $data['t']);
            $backendfaqModel->setData($data)->save();
            $returnData['success'][] = $backendfaqModel->getId();
        } catch (Exception $e) {
            $returnData['error'][] = 'error';
        }
        echo json_encode($returnData);
        exit;
    }
    public function updateFaqAction() {
        $data = json_decode(urldecode($this->getRequest()->getParam('data')), true);
        $backendfaqModel = Mage::getModel('backendfaq/backendfaq');
        try {
            $data = array('question' => $data['q'],
            'answer' => $data['a'],
            'date' => $data['date'],
            'username' => $data['username'],
            'topic' => $data['t']);
            $backendfaqModel->load()->addData($data)->setId($data['returnId'])->save();;
            // $backendfaqModel->setData($data)->save();
            // exit;
            $returnData['success'][] = 'success';
        } catch (Exception $e) {
            $returnData['error'][] = 'error';
        }
        echo json_encode($returnData);
        exit;
    }
    public function eCheckAction(){
        $crm_url = Mage::getStoreConfig('crm_api_credentails/crm_api_credentails_details/api_url');
         $data['echeckid'] =   $this->getRequest()->getParam('echeckid');
         $data['orderID'] =   $this->getRequest()->getParam('orderID');

         //$url = 'http://backorderlist.net/api/updateEcheckId';
         $returnData = $this->sendToCrm($data, $crm_url);
        //echo "Echeck Test";

    }
    public function updateOrderStatusCreditAction(){
        $crm_url = Mage::getStoreConfig('crm_api_credentails/crm_api_credentails_details/api_url');
        $api_key = Mage::getStoreConfig('crm_api_credentails/crm_api_credentails_details/api_key');
        $api_user = Mage::getStoreConfig('crm_api_credentails/crm_api_credentails_details/api_username');
        $api_password = Mage::getStoreConfig('crm_api_credentails/crm_api_credentails_details/api_password');
        $url = $crm_url.'updateStatus';

        $data['orderid'] =   $this->getRequest()->getParam('orderid');
        $orderObj = Mage::getModel('sales/order')->loadByIncrementId($data['orderid']);
         $commentHistory = array();
         foreach($orderObj->getAllStatusHistory() as $comments){
                $commentH =  array();
                $commentH['comment'] = $comments->getData('comment');
                $commentH['status'] = $comments->getData('status');
                $commentH['created_at'] = floatval(strtotime($comments->getData('created_at'))*1000);
                $commentHistory[] = $commentH;
            }
            
            $dataToSend[] = array('orderId'=>$data['orderid'],'commentHistory'=>$commentHistory,'orderStatus'=> $orderObj['status']);
            $ch = curl_init($url);
        $api_post = array('user'=>$api_user, 'pass'=>$api_password, 'api_key'=>$api_key, 'data'=>urlencode(json_encode($dataToSend)));
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
       
         //$returnData = $this->sendToCrm($data, $crm_url);

       
    }
    public function sendToCrm($dataToSend, $url) {
        // $url = $this->crm_url.'updateAddress';
         $api_key = Mage::getStoreConfig('crm_api_credentails/crm_api_credentails_details/api_key');
        $api_user = Mage::getStoreConfig('crm_api_credentails/crm_api_credentails_details/api_username');
        $api_password = Mage::getStoreConfig('crm_api_credentails/crm_api_credentails_details/api_password');
        $crm_url = Mage::getStoreConfig('crm_api_credentails/crm_api_credentails_details/api_url');
        $ch = curl_init($url);
        $api_post = array('user'=>$api_user, 'pass'=>$api_password, 'api_key'=>$api_key, 'data'=>urlencode(json_encode($dataToSend)));
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
         //exit;
        return $Rec_Data;
    }
}