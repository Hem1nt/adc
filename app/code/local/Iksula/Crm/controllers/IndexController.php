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
                /*$order['statusCode'*/
                /*order mail*/
                /* start to get template id respective of order id from system config*/
                $order['statusCode'];
                if(isset($order['statusCode'])){
              $val = $order['statusCode'];
            }
            $templateMapping = array();


            foreach (unserialize(Mage::getStoreConfig("custom_snippet/snippet/email")) as $mapping) {
                if (array_key_exists('list_template', $mapping)) {
                    $templateMapping[$mapping['list_template']] = $mapping['magento_template'];
                }
            }
            if($templateMapping[$val] != ""){
                $templateId = $templateMapping[$val];
            }
            else{
                $templateId = Mage::getStoreConfig("custom_snippet/snippet/default_template");
            }
            /* end to get template id respective of order id from system config*/

                $tempID = $templateId;

                $mailTemplate = Mage::getModel('core/email_template');
                $translate  = Mage::getSingleton('core/translate');

                $template_collection =  $mailTemplate->load($templateId);
                $template_data = $template_collection->getData();

                 /*print_r($template_data);
                 exit;*/
                if(!empty($template_data)){
                    $templateId = $template_data['template_id'];
                    $mailSubject = $template_data['template_subject'];
                    //fetch sender data from Adminend > System > Configuration > Store Email Addresses > General Contact
                    $from_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email
                    $from_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name

                    $sender = array('name'  => $from_name,'email' => $from_email);
                    $customer['order'] =  $orderObj;
                    $customer['amount']= sprintf("%0.2f", $orderObj->getGrandTotal());
                    $formattedPrice = Mage::helper('core')->currency($customer['amount'], true, false);//exit;
                    $customer['order_amount']= $formattedPrice;
                    $customer['cust_name'] =  $orderObj->getBillingAddress()->getName();
                    $customer['order_id']= $orderObj->getData('increment_id');
                    $customer['order_entity_id']= $orderObj->getId();

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
                    $paymentLinkUrl = Mage::getStoreConfig('custom_snippet/snippet/paymentlink');
                        $customer['link'] = $paymentLinkUrl.'?order_id='.base64_encode($customer['order_entity_id']);
                    //$customer['customer_email'] = $order->getData("email");
                    $customer['supply_issue_message'] = $orderObj->getData('supply_issue_message');
                    $customer['dispatcher_message'] = $orderObj->getData('dispatcher_message');
                    // $customer['paymentinfo_message'] = $orderObj->getData('paymentinfo_message');
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
                     /*echo "Name is &nbsp;".$name;
                     print_r($vars);
                    exit;  */
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
                    //$address->setLastname($name[1]);
                    $address->setStreet($data['street']);
                    $address->setCity($data['city']);
                    $address->setCompany($data['company']);
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
                    //$address->setLastname($name[1]);
                    $address->setStreet($data['street']);
                    $address->setCompany($data['company']);
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
    public function updateSupplyIssueCrmAction(){
        $data = json_decode($this->getRequest()->getParam('data'),true);
        //echo "<pre>"; print_r($data);
        $orderId = $data['order_id'];
        $supply_issue_message = $data['supply_issue_message'];
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        //echo "<pre>"; print_r($data);exit;
        if($order){
            try {
                $order->setSupplyIssueMessage($supply_issue_message)->save();
                //echo "<pre>"; print_r($order->getData());setDispatcherMessage exit;
                $returnData['success'][] = $orderId;
            } catch(Exception $e) {
                $returnData['error'][] = $orderId;
            }
        } else {
            $returnData['delete'][] = $orderId;
        }
        echo json_encode($returnData);exit;
    }
    public function updateDescriptorInfoCrmAction(){
        $data = json_decode($this->getRequest()->getParam('data'),true);
        // echo "<pre>"; print_r($data);
        $orderId = $data['order_id'];
        $dispatcher_message = $data['dispatcher_message'];
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        //echo "<pre>"; print_r($data);exit;
        if($order){
            try {
                $order->setDispatcherMessage($dispatcher_message)->save();
                echo "<pre>"; print_r($order->getData());
                $returnData['success'][] = $orderId;
            } catch(Exception $e) {
                $returnData['error'][] = $orderId;
            }
        } else {
            $returnData['delete'][] = $orderId;
        }
        echo json_encode($returnData);exit;
    }

    public function updatePaymentInfoCrmAction(){
        $data = json_decode($this->getRequest()->getParam('data'),true);
        echo "<pre>"; print_r($data);
        $orderId = $data['order_id'];
        $paymentinfo_message = $data['paymentinfo_message'];
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        //echo "<pre>"; print_r($data);exit;
        if($order){
            try {
                $order->setPaymentinfoMessage($paymentinfo_message)->save();
                echo "<pre>"; print_r($order->getData()); exit;
                $returnData['success'][] = $orderId;
            } catch(Exception $e) {
                $returnData['error'][] = $orderId;
            }
        } else {
            $returnData['delete'][] = $orderId;
        }
        echo json_encode($returnData);exit;
    }

    public function updateFromDateCrmAction(){
        $data = json_decode($this->getRequest()->getParam('data'),true);
        //echo "<pre>"; print_r($data);
        $orderId = $data['order_id'];
        $from_date = $data['from_date'];
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        //echo "<pre>"; print_r($data);exit;
        if($order){
            try {
                $order->setFromdateMessage($from_date)->save();
                echo "<pre>"; print_r($order->getData()); exit;
                $returnData['success'][] = $orderId;
            } catch(Exception $e) {
                $returnData['error'][] = $orderId;
            }
        } else {
            $returnData['delete'][] = $orderId;
        }
        echo json_encode($returnData);exit;
    }

    public function updateToDateCrmAction(){
        $data = json_decode($this->getRequest()->getParam('data'),true);
        echo "<pre>"; print_r($data);
        $orderId = $data['order_id'];
        $to_date = $data['to_date'];
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        //echo "<pre>"; print_r($data);exit;
        if($order){
            try {
                $order->setTodateMessage($to_date)->save();
                echo "<pre>"; print_r($order->getData());exit;
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

    public function OrderAction($data) {

           $formdata = /*json_decode(*/urldecode($this->getRequest()->getParam('data'))/*, true)*/;
           $order = Mage::getModel('sales/order')->loadByIncrementId($formdata);
           $biilingid = $order->getData('billing_address_id');

            $shippingAddress = Mage::getModel('sales/order_address')->load($order->getShippingAddressId());
            $billingAddress = Mage::getModel('sales/order_address')->load($order->getBillingAddressId());

           $result = array();
           $result['order'] = $order->getData();
           $customer_name = $result['order']['customer_lastname'];
           $customer_name_lower = strtolower($customer_name);

            $orderData = $order->getData();

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

            $gender = $result['order']['customer_gender'];
            $callforoffers = $orderData['callforoffers'];
            $timetocall = $orderData['timetocall'];



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
           $result['shipping'] = $shippingAddress->getData();
           $result['billing'] = $billingAddress->getData();

           $result['billing_name'] =  $result['billing']['firstname'].' '.$result['billing']['lastname'];
           $result['shipping_name'] = $result['shipping']['firstname'].' '.$result['shipping']['lastname'];

           $result['payment_title'] = $order->getPayment()->getMethodInstance()->getTitle();
           $result['payment_method'] = $order->getPayment()->getMethodInstance()->getCode();
           $result['product_details'] = $products;
           $result['commentHistory'] = $commentHistory;
           $result['medical_history'] = $medical_history;
           $result['customerId'] = $customerId;
           $result['groupId'] = $groupId;
           $result['gender'] = $gender;
           $result['callforoffers'] = $callforoffers;
           $result['timetocall'] = $timetocall;
           $result['customer_name_lower'] = $customer_name_lower;
           $result['ip'] = $ip;

           echo json_encode($result);

        }

        public function AllorderAction() {
            $time = time();
            $to = date('Y-m-d H:i:s', $time);
            $lastTime = $time - 86400; // 60*60*24
            $from = date('Y-m-d H:i:s', $lastTime);
            $order_items = Mage::getModel('sales/order')->getCollection()
                ->addAttributeToSelect('increment_id')
                ->addAttributeToSelect('created_at')
                ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to))
                ->load();

            echo json_encode($order_items->getData());
        }
}
