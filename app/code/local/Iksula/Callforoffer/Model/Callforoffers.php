<?php

class Iksula_Callforoffer_Model_Callforoffers extends Mage_Core_Model_Abstract
{
    protected function _construct(){

       $this->_init("callforoffer/callforoffers");

    }

    public function getUserName($email) {
    	$customer_email = $email;
	    if($customer_email) {
			$customer = Mage::getModel("customer/customer");
			$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
			$customer->loadByEmail($customer_email); //load customer by email id
			$name = array('firstname' => 'User', 'lastname' => '');
			if($customer->getSize()>0) {
				return $customer->getData();;
			}
			else {
				return $name;
			}			
	    }
    }
    public function CheetaApi($custEmail="", $subId="", $aId="", $fname="User", $lname="") {
		//echo "Hello"; exit;
		// $this->CheetaApi($email, 2094099241, 2093962299, $customerData['firstname'], $customerData['lastname']);
		$pathForCookie = "C:\\my_cookie_file";
		"crulReuest Function: OK<br>";
		//set URI of login1 call
		$login_uri = "https://ebm.cheetahmail.com/api/login1";

		//set POST parameters for login1 call
		$login_params = array(
			"name=".urlencode("API_iKsula"),
			"cleartext=".urlencode("ik_Sula_CM")
			);
		//url-ify the POST parameters
		$param_string = implode('&', $login_params);
		//exit;
		//open a cURL connection

		$curl = curl_init($login_uri);
		if ($curl){
			//var_dump($curl); exit;
			// set request method=POST
			curl_setopt($curl, CURLOPT_POST, true);
			// set cURL POST parameters
			curl_setopt($curl, CURLOPT_POSTFIELDS, $param_string);
			// tell cURL to return server response for variable storage
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			// store cookie in specified file
			
			curl_setopt($curl, CURLOPT_COOKIEJAR, $pathForCookie);
			// execute login1 POST request and store server response
			$response = trim(curl_exec($curl));
			// if there was a cURL error, display it
			if(curl_errno($curl)) {
				//-------echo "error: ".curl_error($curl)."<br/>";
			}
			// close the cURL connection
			curl_close($curl);
			//display server response
			// echo "login server response: $response\n";
			//-------echo "login server response: $response\n<br/>";
			//if authentication successful...
			if ($response == "OK"){
				$ebmtrigger_uri = "https://ebm.cheetahmail.com/api/setuser1";
				$embtrigger_params = array(
					"email=".urlencode($custEmail),
					"aid=".urlencode($aId),
					"sub=".urlencode($subId),
					"FNAME=".urlencode($fname),
					"LNAME=".urlencode($lname)
				);
				$param_string = implode('&', $embtrigger_params);
				$curl = curl_init($ebmtrigger_uri);
				if($curl) {
					curl_setopt($curl, CURLOPT_POST, true);
					curl_setopt($curl, CURLOPT_POSTFIELDS, $param_string);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					//specify location of cookie file
					curl_setopt($curl, CURLOPT_COOKIEFILE, $pathForCookie);
					$response = trim(curl_exec($curl));
					
					if (curl_errno($curl)) {
						//-------echo "error: ".curl_error($curl)."<br/>";
					}
					curl_close($curl);
					//-------echo "<br/>ebmtrigger server response: $response<br/>";
					
				}
			}
		}
	}
}
	 