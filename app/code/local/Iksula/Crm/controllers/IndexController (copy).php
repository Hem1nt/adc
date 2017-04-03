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

                $orderObj->sendOrderUpdateEmail(true, $mainComment);
                
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
        echo json_encode($returnData); exit;           
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
        echo json_encode($returnData);
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
}