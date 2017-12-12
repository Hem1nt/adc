<?php

class Iksula_Birthday_Model_Observer
{
    public function sendBirthayEmail()
    {
        //Mage::helper('birthday/data')->customerInfo();
    }
    
    public function sendbirthdayCoupon(){	
       $status = Mage::getStoreConfig('birthday_offer/birthday_group/status');
       if($status){
            $this->customerBirthdayCoupon();
       }	
    }

    public function customerBirthdayCoupon() 
    {
        
        $currentTimestamp = Mage::getModel('core/date')->timestamp(time());
        $currentDate = date('Y-m-d h:i:s', $currentTimestamp);
        $cronDate = date('m-d 00:00:00', $currentTimestamp);
        
        $expireTimestamp = strtotime('+30 days', $currentTimestamp);
        $expireDate = date('Y-m-d', $expireTimestamp);
        
        $model = Mage::getModel('birthday/birthday');
        
        //this collection gets all users which have birthday on today
        $customer = Mage::getModel("customer/customer")->getCollection();
        //$customer->addFieldToFilter('dob', array('like' => '%'.date("m").'-'.date("d").' 00:00:00'));
        $customer->addFieldToFilter('dob', array('like' => '%'.$cronDate));
        $customer->addNameToSelect();
        $items = $customer->getItems();
        $email_content = '<html><body><h2>Birthday Coupon List</h2><table border="1" rules="all" style="border-color: #666;" cellpadding="10">';
        $email_counter = 1;
        foreach($items as $item)
        {
            $customer_id = $item->getData('entity_id');
            $email = $item->getData('email');
            $firstname = $item->getData('firstname');
            $lastname = $item->getData('lastname');
            $groupId = $customer->getData('group_id');

            $groupData = Mage::getModel('customer/group')->getCollection()->addFieldToFilter('customer_group_id',$groupId);
            $firstItem = $groupData->getFirstItem();
            $groupName = $firstItem->getData('customer_group_code');

            $customername = $firstname." ".$lastname;
            $customer_dob = date('Y-m-d', strtotime($item->getData('dob')));
            $_orders = $this->_orderCollection()->addFieldToFilter('customer_email',$email);                        
            $_orderCount = $_orders->getSize();
            if($_orderCount > 0){
                $coupon_helper = $this->PersonSpecificCoupon();
                $this->sendMail($email, $customername, $coupon_helper);
                $dataArray = array(
                'customer_id' =>$customer_id,
                'first_name' =>$firstname,
                'last_name' =>$lastname,
                'email' =>$email,
                'coupon' =>$coupon_helper,
                'birthdate' =>$customer_dob,
                'anniversary' =>$customer_dob,
                'customer_group' =>$groupName,
                'coupon_created_date'=>$currentDate,
                'coupon_expire_date' =>$expireDate,
                'coupon_status' =>'0',
                'email_send' => 1,
                'no_of_email_send' =>'',
                'date_mail_to_send'=>$currentDate,
                );
                $model->setData($dataArray)->save();

                $email_content .= '<tr>';
                $email_content .= '<td>'.$email_counter.'</td><td>'.$email.'</td><td>'.$coupon_helper.'</td>';
                $email_content .= '</tr>';
                $email_counter++;
            }
            
        }
        $email_content .= '</table></body></html>';

        $cron_notify_email = Mage::getStoreConfig('birthday_offer/birthday_group/cron_notify_email');
        
        //$to = "manoj.chowrasiya@iksula.com";
        $to = "hemant.r@iksula.com";
 
        $subject = 'Customers Birthday Coupon Cron';

        $headers = "From: noreply@alldaychemist.com" . "\r\n";
        $headers .= "CC: ".$cron_notify_email."\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        mail($to,$subject,$email_content,$headers);


    }

    public function PersonSpecificCoupon()
    {
        $parameters = array(
            'count'=>1,
            'format'=>'alphanumeric',
            'dash_every_x_characters'=>4,
            'prefix'=>'HB-',
            'length'=>10
        );

        $ruleId = Mage::getStoreConfig('birthday_offer/birthday_details/birthday_rule');
        // exit;
        $rule = Mage::getModel('salesrule/rule')->load($ruleId);
        $generator = Mage::getModel('salesrule/coupon_massgenerator');
        $generator->setFormat( Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_ALPHANUMERIC );
        $generator->setLength( !empty($parameters['length'])? (int) $parameters['length'] : 10);
        $generator->setPrefix( !empty($parameters['prefix'])? $parameters['prefix'] : '');
        $generator->setSuffix( !empty($parameters['suffix'])? $parameters['suffix'] : '');
        $generator->setSuffix('');
        $rule->setCouponCodeGenerator($generator);
        $rule->setCouponType( Mage_SalesRule_Model_Rule::COUPON_TYPE_AUTO );
        $count = !empty($parameters['count'])? (int) $parameters['count'] : 1;
        $codes = array();
        for( $i = 0; $i < $count; $i++ ){
          $coupon = $rule->acquireCoupon();
          $coupon
            ->setType(Mage_SalesRule_Helper_Coupon::COUPON_TYPE_SPECIFIC_AUTOGENERATED)
            ->save();
         
          $code = $coupon->getCode();
          $codes[] = $code;
        }
        return $codes[0];
    }
    public function _orderCollection()
    {
        return Mage::getModel('sales/order')->getCollection();
    }

    public function sendMail ($email ,$customername, $couponCode) 
    {
      $this->curlRequest ($email, $customername, $couponCode);
    }

    public function curlRequest($email, $customername, $couponCode)
    {
        // echo "string";exit;
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

            // echo "login server response: $response\n<br>";
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
                    // echo "ebmtrigger server response: $response\n"; 

                }
            }  
        }
    }

}