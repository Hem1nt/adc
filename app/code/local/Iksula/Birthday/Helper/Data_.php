<?php

class Iksula_Birthday_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function PersonSpecificCoupon($email)
	{
		$coupon_code_check = 'happy-'.$email;

		//$oCoupon = Mage::getModel('salesrule/rule')->load($coupon_code_check, 'code');
		
        $currentTimestamp = Mage::getModel('core/date')->timestamp(time());
		$expireTimestamp = strtotime('+7 days', $currentTimestamp);
        
        $currentDate = date('Y-m-d', $currentTimestamp);
        $expireDate = date('Y-m-d', $expireTimestamp);
        //echo $currentDate.'<br>';
        //echo $expireDate; exit;

        $coupon = Mage::getModel('salesrule/rule');

    	$couponArray = Mage::getModel('salesrule/rule')->getCollection()->addfieldtoFilter('code', $coupon_code_check);

    	$couponCount = $couponArray->getData();
    		
    	if(count($couponCount)==0) {
    		$value =rand(0,100);
    		$couponcode = 'happy-'.$email;
    	
    		$coupon->setName('Birthday offer')
    			->setDescription('this is a description')
    			->setFromDate($currentDate)
    			->setToDate($expireDate)
    			->setCouponType(2)
    			->setCouponCode($couponcode)
    			->setUsesPerCoupon(1)
    			->setUsesPerCustomer(1)
    			->setCustomerGroupIds(array(1,2,3,4,5,6)) //an array of customer group ids
    			->setIsActive(1)
    			->setConditionsSerialized('a:6:{s:4:"type";s:32:"salesrule/rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";}')
    			->setActionsSerialized('a:6:{s:4:"type";s:40:"salesrule/rule_condition_product_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";}')
    			->setStopRulesProcessing(0)
    			->setIsAdvanced(1)
    			->setProductIds('')
    			->setSortOrder(0)
    			->setSimpleAction('by_percent')
    			->setDiscountAmount(10)
    			->setDiscountQty(null)
    			->setDiscountStep('0')
    			->setSimpleFreeShipping('0')
    			->setApplyToShipping('0')
    			->setIsRss(0)
    			->setWebsiteIds(array(1));
     	 	 $coupon->save();

     	 	 return $couponcode;

    	}
        else {
    		return 'exists:'.$coupon_code_check;			
    	}
    }

    public function customerInfo() 
    {
        $currentTimestamp = Mage::getModel('core/date')->timestamp(time());
        $currentDate = date('Y-m-d h:i:s', $currentTimestamp);
        $cronDate = date('m-d 00:00:00', $currentTimestamp);
        //echo $cronDate;exit;
        
        $expireTimestamp = strtotime('+7 days', $currentTimestamp);
        $expireDate = date('Y-m-d', $expireTimestamp);

        $model = Mage::getModel('birthday/birthday');
        
        //this collection gets all users which have birthday on today
        $customer = Mage::getModel("customer/customer")->getCollection();
        //$customer->addFieldToFilter('dob', array('like' => '%'.date("m").'-'.date("d").' 00:00:00'));
        $customer->addFieldToFilter('dob', array('like' => '%'.$cronDate));
        $customer->addNameToSelect();
        $items = $customer->getItems();
        foreach($items as $item)
        {
            // echo "<pre>";
            // print_r($item->getData());exit;
            $customer_id = $item->getData('entity_id');
            $email = $item->getData('email');
            $firstname = $item->getData('firstname');
            $lastname = $item->getData('lastname');
            $customername = $firstname." ".$lastname;
            $customer_dob = date('Y-m-d', strtotime($item->getData('dob')));
            
            //For Testing uncomment this loop
            /*if($email == 'moiz.k@iksula.com' || $email == 'manoj.chowrasiya@iksula.com' || $email == 'kundan@derricwood.com')
            {
                //echo $email;exit;
                $coupon_helper = $this->PersonSpecificCoupon($email);
                if (strpos($coupon_helper,':'))
                {
                    $couponCode = explode(':',$coupon_helper);
                    $coupon = $couponCode[1];
                    
                    $customers = $model->getCollection()->addFieldToFilter('email_send',0);
                    foreach ($customers as $data) {
                        //echo "<pre>"; print_r($data->getData());
                        $this->sendMail($email, $customername, $coupon);
                        
                        $birthday_id = $data->getData('birthday_id');
                        $model->load($birthday_id);
                        $dataArr = array('email_send' => 1);
                        $model->addData($dataArr)->save();
                    }
                }

                else
                {
                    $this->sendMail($email, $customername, $coupon_helper);
                    $dataArray = array(
                    'customer_id' =>$customer_id,
                    'first_name' =>$firstname,
                    'last_name' =>$lastname,
                    'email' =>$email,
                    'coupon' =>$coupon_helper,
                    'birthdate' =>$customer_dob,
                    'anniversary' =>$customer_dob,
                    'customer_group' =>'',
                    'coupon_created_date'=>$currentDate,
                    'coupon_expire_date' =>$expireDate,
                    'coupon_status' =>'0',
                    'email_send' => 1,
                    'no_of_email_send' =>'',
                    );
                    $model->setData($dataArray)->save();
                }
            }*/

            $coupon_helper = $this->PersonSpecificCoupon($email);
            if (strpos($coupon_helper,':'))
            {
                $couponCode = explode(':',$coupon_helper);
                $coupon = $couponCode[1];
                
                $customers = $model->getCollection()->addFieldToFilter('email_send',0);
                foreach ($customers as $data) {
                    //echo "<pre>"; print_r($data->getData());
                    $this->sendMail($email, $customername, $coupon);
                    
                    $birthday_id = $data->getData('birthday_id');
                    $model->load($birthday_id);
                    $dataArr = array('email_send' => 1);
                    $model->addData($dataArr)->save();
                }
            }

            else
            {
                $this->sendMail($email, $customername, $coupon_helper);
                $dataArray = array(
                'customer_id' =>$customer_id,
                'first_name' =>$firstname,
                'last_name' =>$lastname,
                'email' =>$email,
                'coupon' =>$coupon_helper,
                'birthdate' =>$customer_dob,
                'anniversary' =>$customer_dob,
                'customer_group' =>'',
                'coupon_created_date'=>$currentDate,
                'coupon_expire_date' =>$expireDate,
                'coupon_status' =>'0',
                'email_send' => 1,
                'no_of_email_send' =>'',
                );
                $model->setData($dataArray)->save();
            }
        }
    }

    public function sendMail ($email ,$customername, $couponCode) 
    {
      $this->curlRequest ($email, $customername, $couponCode);
    }

    public function curlRequest($email, $customername, $couponCode)
    {
        $login_cheetahmail_curi = 'https://app.cheetahmail.com/api/login1';
        $login_param_name = 'API_iKsula';
        $login_param_cleartext = 'ik_Sula_CM';
        $login_ebmtrigger_uri = 'https://ebm.cheetahmail.com/ebm/ebmtrigger1';
        $login_aid = 2094210672;
        $login_eid = 245845;//exit;

        //echo "crulReuest Function: OK<br>";

        $login_uri = $login_cheetahmail_curi;

        $login_params = array(
         "name=".urlencode($login_param_name),
         "cleartext=".urlencode($login_param_cleartext)
         );
          //url-ify the POST parameters
        $param_string = implode('&', $login_params);
          //open a cURL connection
        $curl = curl_init($login_uri);
          //if cURL connection successfully opened...
        if ($curl)
        {
            //set request method=POST
            curl_setopt($curl, CURLOPT_POST, true);
            //set cURL POST parameters
            curl_setopt($curl, CURLOPT_POSTFIELDS, $param_string);
            //tell cURL to return server response for variable storage
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            //store cookie in specified file
            curl_setopt($curl, CURLOPT_COOKIEJAR, "my_cookie_file");
            //execute login1 POST request and store server response
            $response = trim(curl_exec($curl));
            //if there was a cURL error, display it
            if (curl_errno($curl)) {
            echo "error: ".curl_error($curl);
            }
            //close the cURL connection
            curl_close($curl);

            //echo "login server response: $response\n<br>";
            //if authentication successful...
            if ($response == "OK")
            {
                $ebmtrigger_uri = $login_ebmtrigger_uri;
                $embtrigger_params = array(
                "aid=".$login_aid,
                "email=".$email,
                "eid=".$login_eid,
                "req=1",
                "FNAME=".$customername,
                "COUPONCODE=".$couponCode,
                "REMOVE="." "
                );
                $param_string = implode('&', $embtrigger_params);
                $curl = curl_init($ebmtrigger_uri);
                if ($curl)
                {
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $param_string);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_COOKIEFILE, "my_cookie_file");
                    $response = trim(curl_exec($curl));

                    if (curl_errno($curl)) {
                        echo "error: ".curl_error($curl); 
                    }
                    curl_close($curl);
                    //echo "ebmtrigger server response: $response\n"; 

                }
            }  
        }
    }
}
	 