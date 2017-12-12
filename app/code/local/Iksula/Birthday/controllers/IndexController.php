<?php
class Iksula_Birthday_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {      
	  $this->loadLayout();   
      $this->renderLayout(); 	  
    }

      public function createCouponAction() {  

      // Mage::helper('birthday/data')->PersonSpecificCoupon('manojiksula@gmail.com');    
        echo '<pre>';
      $collection = Mage::getResourceModel('customer/customer_collection')
            ->joinAttribute('dob','customer/dob', 'entity_id');
        print_r($collection->getSize());
        print_r($collection->getData());
      $this->loadLayout();   
      $this->renderLayout();    
    }

    public function cronlogAction()
    {
        //$to = "manoj.chowrasiya@iksula.com";
        $to = "hemant.r@iksula.com";
        $subject = "Cron Hit Birth Day New";
        $txt = "Hello world!";
        $headers = "From: noreply@alldaychemist.com" . "\r\n" .
        "CC: manohar.p@iksula.com";

        mail($to,$subject,$txt,$headers);
        Mage::log('cron running',null,'cron_testing.log');
    }

    public function testAction(){
        // birthday/index/test
        Mage::getModel('birthday/observer')->sendbirthdayCoupon();
        $email =  Mage::app()->getRequest()->getParam('email');
        // echo $coupon_helper = Mage::helper('birthday/data')->PersonSpecificCoupon($email);
        if($email){
            $coupon_helper = Mage::helper('birthday/data')->sendMail($email,'manoj','happy-'.$email);
        }
    }

    public function testhbAction(){
        // birthday/index/test
        $email =  Mage::app()->getRequest()->getParam('email');
        // echo $coupon_helper = Mage::helper('birthday/data')->PersonSpecificCoupon($email);
        if($email){
            $coupon_helper = Mage::helper('birthday/data')->adhocBirthdayEmail($email);
        }
    }
    
    public function savePersonSpecificCouponAction() {  
        $currentTimestamp = Mage::getModel('core/date')->timestamp(time());
        $currentDate = date('Y-m-d h:i:s', $currentTimestamp);
        $model = Mage::getModel('birthday/birthday');
        $dataArray = array(
            'customer_id' =>'1234',
            'first_name' =>'fgdfxh',
            'last_name' =>'gfhgf',
            // 'email' =>'manojiksula@gmail',
            // 'coupon' =>'slap-manojiksula@gmail.com',
            'email' =>'hemant.r@iksula.com',
            'coupon' =>'slap-hemant.r@iksula.com',
            'birthdate' =>time(),
            'anniversary' =>time(),
            'customer_group' =>'',
            'coupon_created_date'=>$currentDate,
            'coupon_expire_date' =>$currentDate,
            'coupon_status' =>'0',
            'no_of_email_send' =>'',

        );
        $model->setData($dataArray)->save();

    }

    public function couponMailAction($email, $firstname, $lastname, $couponCode)
    {
        $msg = "<table width='100%' cellspacing='0' cellpadding='15' /*bgcolor='#F1EFE3'*/>";
        $msg .= "<tr>";
        $msg .= "<td><div style='font-family:Arial, Helvetica, sans-serif;font-size:12px;color:#666666;width:600px;'>";
        $msg .= "<b>Hello ".$firstname." ".$lastname."</b><br><br>";
        $msg .= "<b>Coupon Code: </b>".$couponCode."<br><br>";
        
        $msg .= "<br><br>Regards,<br>";

        $from = "info@alldaychemist.com";
        $to = $email;

        $subject = "Slap Coupon";
        $body = $msg;
        $client = 'AllDayChemist';

        $headers = "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\n";

        $headers .= "From:".$client."<".$from.">\n";
        $headers .= "Reply-To:".$firstname."<".$from.">\n";

        mail($to, $subject, $body, $headers);
    }

    public function customerInfoAction(){

        $this->cronlogAction();
        exit;
        
        // $terminal = Mage::app()->getRequest()->getParam('param');
        // Mage::log($terminal,null,'bithday_cron.log');
        // if($terminal == 'terminal'){
        //    $this->customerInfoNew();
        // }
    }

    // public function testmailfunctionAction(){
    // 	$to = "manoj.chowrasiya@iksula.com";
    // 	$subject = "Cron Hit Birth Day";
    // 	$txt = "Hello world!";
    // 	$headers = "From: moizk007@gmail.com" . "\r\n" .
    // 	"CC: somebodyelse@example.com";

    // 	mail($to,$subject,$txt,$headers);
    //     // $coupon_helper = Mage::helper('birthday/data')->sendMail('manoj.chowrasiya@iksula.com','manoj');
    // }

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

    public function customerInfoNew() 
    {
        // echo "here";exit;
        
        $this->reportSend();
        // exit;
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
        foreach($items as $item)
        {
            // echo "<pre>";
            // print_r($item->getData());exit;
            $customer_id = $item->getData('entity_id');
            $email = $item->getData('email');
            $firstname = $item->getData('firstname');
            $lastname = $item->getData('lastname');
            $groupId = $item->getData('group_id');

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
                  
            }
            
        }
    }

    public function reportSend()
    {
        //$to = "manoj.chowrasiya@iksula.com";
        $to = "hemant.r@iksula.com";
        $subject = "Birthday Coupon Created";
        $txt = "Birthday Coupon Created";
        $headers = "From: noreply@alldaychemist.com" . "\r\n" .
        "CC: manohar.p@iksula.com";
//kundan@derricwood.com,
        mail($to,$subject,$txt,$headers);
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

    public function _orderCollection()
    {
        return Mage::getModel('sales/order')->getCollection();
    }

    public function deleteCouponAction(){
        $rule = Mage::getStoreConfig('birthday_offer/birthday_details/birthday_rule');
        $couponData = Mage::getModel('salesrule/coupon')->getCollection()->addFieldToFilter('rule_id',$rule);
        // foreach ($couponData->getData() as $rule1) {
        //      echo '<pre>';
        //      print_r($rule1);
        // }
            echo '<pre>';
            print_r($couponData->getData());
            exit;
    }
}