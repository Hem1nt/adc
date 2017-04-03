<?php
class Iksula_Refillreminder_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function curlRequest($email,$status,$eid,$fname,$message,$producttitle,$date_of_reminder){
		// echo "<textarea>".$message."</textarea>"; //exit; 
		// echo '<br/>';
		// echo "crulReuest Function: OK<br>";
		// if($message==''){
		// 	$message = 'message';
		// }
		$login_uri = "https://app.cheetahmail.com/api/login1";
		$login_params = array(
			"name=".urlencode("API_iKsula"),
			"cleartext=".urlencode("ik_Sula_CM")
			);
		$param_string = implode('&', $login_params);
		$curl = curl_init($login_uri);
		if ($curl){
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $param_string);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_COOKIEJAR, "C:\\my_cookie_file");
			$response = trim(curl_exec($curl));


				// echo "error: ".curl_error($curl);
				// echo "<br>";
			}
			curl_close($curl);
			// echo "login server response: $response\n<br>";
				// echo "<br>";
			if ($response == "OK"){
				$ebmtrigger_uri = "https://ebm.cheetahmail.com/ebm/ebmtrigger1";
				$embtrigger_params = array(
					"aid=2094210672",
					"email=".$email,
					"eid=".$eid,
					"req=1",				
					"PRODUCTDETAIL=".urlencode($message),
					"PRODUCTTITLE=".urlencode($producttitle),
					"REFILLDATE=".urlencode($date_of_reminder),
					"FNAME=".ucwords(ucfirst(strtolower($fname)))
				);
				$param_string = implode('&', $embtrigger_params);
				$curl = curl_init($ebmtrigger_uri);
				if ($curl){
					curl_setopt($curl, CURLOPT_POST, true);
					curl_setopt($curl, CURLOPT_POSTFIELDS, $param_string);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					//specify location of cookie file
					curl_setopt($curl, CURLOPT_COOKIEFILE, "C:\\my_cookie_file");
					$response = trim(curl_exec($curl));
					
					if (curl_errno($curl)) {
						echo "error: ".curl_error($curl); 
							// echo "<br>";
					}
					curl_close($curl);
					//echo "ebmtrigger server response: $response\n"; 
						// echo "<br>";
					
				}
			}
		// }
	}
	public function curlRequestOrderReminder($email,$status,$eid,$fname,$message,$producttitle,$totalprice){
		//echo "<textarea>".$message."</textarea>"; //exit; 
		// echo '<br/>';
		// echo "crulReuest Function: OK<br>";
		// if($message==''){
		// 	$message = 'message';
		// }
		$login_uri = "https://app.cheetahmail.com/api/login1";
		$login_params = array(
			"name=".urlencode("API_iKsula"),
			"cleartext=".urlencode("ik_Sula_CM")
			);
		$param_string = implode('&', $login_params);
		$curl = curl_init($login_uri);
		if ($curl){
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $param_string);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_COOKIEJAR, "C:\\my_cookie_file");
			$response = trim(curl_exec($curl));


				// echo "error: ".curl_error($curl);
				// echo "<br>";
			}
			curl_close($curl);
			// echo "login server response: $response\n<br>";
				// echo "<br>";
			if ($response == "OK"){
				$ebmtrigger_uri = "https://ebm.cheetahmail.com/ebm/ebmtrigger1";
				$embtrigger_params = array(
					"aid=2094210672",
					"email=".$email,
					"eid=".$eid,
					"req=1",				
					"PRODUCTDETAIL=".urlencode($message),
					"PRODUCTTITLE=".urlencode($producttitle),
					"TOTALVALUE=".urlencode($totalprice),
					// "REFILLDATE=".urlencode($date_of_reminder),
					"FNAME=".ucwords(ucfirst(strtolower($fname)))
				);
				$param_string = implode('&', $embtrigger_params);
				$curl = curl_init($ebmtrigger_uri);
				if ($curl){
					curl_setopt($curl, CURLOPT_POST, true);
					curl_setopt($curl, CURLOPT_POSTFIELDS, $param_string);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					//specify location of cookie file
					curl_setopt($curl, CURLOPT_COOKIEFILE, "C:\\my_cookie_file");
					$response = trim(curl_exec($curl));
					
					if (curl_errno($curl)) {
						echo "error: ".curl_error($curl); 
							// echo "<br>";
					}
					curl_close($curl);
				//echo "ebmtrigger server response: $response\n"; 
				//		// echo "<br>";
					
				}
			}
		// }
	}
	public function curlRequestReminder($email,$status,$eid,$fname,$message,$producttitle){
		//echo "<textarea>".$message."</textarea>"; //exit; 
		// echo '<br/>';
		// echo "crulReuest Function: OK<br>";
		// if($message==''){
		// 	$message = 'message';
		// }
		$login_uri = "https://app.cheetahmail.com/api/login1";
		$login_params = array(
			"name=".urlencode("API_iKsula"),
			"cleartext=".urlencode("ik_Sula_CM")
			);
		$param_string = implode('&', $login_params);
		$curl = curl_init($login_uri);
		if ($curl){
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $param_string);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_COOKIEJAR, "C:\\my_cookie_file");
			$response = trim(curl_exec($curl));


				// echo "error: ".curl_error($curl);
				// echo "<br>";
			}
			curl_close($curl);
			// echo "login server response: $response\n<br>";
				// echo "<br>";
			if ($response == "OK"){
				$ebmtrigger_uri = "https://ebm.cheetahmail.com/ebm/ebmtrigger1";
				$embtrigger_params = array(
					"aid=2094210672",
					"email=".$email,
					"eid=".$eid,
					"req=1",				
					"PRODUCTDETAIL=".urlencode($message),
					// "REFILLDATE=".urlencode($producttitle),
					"PRODUCTTITLE=".urlencode($producttitle),
					// productdetail
					"FNAME=".ucwords(ucfirst(strtolower($fname)))
				);
				$param_string = implode('&', $embtrigger_params);
				$curl = curl_init($ebmtrigger_uri);
				if ($curl){
					curl_setopt($curl, CURLOPT_POST, true);
					curl_setopt($curl, CURLOPT_POSTFIELDS, $param_string);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					//specify location of cookie file
					curl_setopt($curl, CURLOPT_COOKIEFILE, "C:\\my_cookie_file");
					$response = trim(curl_exec($curl));
					
					if (curl_errno($curl)) {
						echo "error: ".curl_error($curl); 
							// echo "<br>";
					}
					curl_close($curl);
					//echo "ebmtrigger server response: $response\n"; 
						// echo "<br>";
					
				}
			}
		// }
	}

	public function sendMailReminder($email,$status,$eid,$fname,$message,$producttitle) 
	{	
		$this->curlRequestReminder($email,$status,$eid,$fname,$message,$producttitle);
	}
	public function sendMail($email,$status,$eid,$fname,$message,$producttitle,$date_of_reminder) 
	{	
		$this->curlRequest($email,$status,$eid,$fname,$message,$producttitle,$date_of_reminder);
	}

	public function sendMailOrderReminder($email,$status,$eid,$fname,$message,$producttitle,$totalprice) 
	{	
		$this->curlRequestOrderReminder($email,$status,$eid,$fname,$message,$producttitle,$totalprice);
	}

	/*
	* pass the sku and get parent
	* input parameter : child sku 
	* output parameter : parent object 
	*/

	public function getParentproduct(int $sku){
		$product = Mage::getModel('catalog/product');
		$productobject = $product->load($product->getIdBySku($sku));
		$ProductId = $product->getIdBySku($sku);
		if($productobject->getTypeId() == 'simple'){
				//product_type_grouped
			$parentIds = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($productobject->getId());
				//product_type_configurable
			if(!$parentIds){
				$parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($productobject->getId());
			}	
				//product_type_bundle
			if(!$parentIds){
				$parentIds = Mage::getModel('bundle/product_type')->getParentIdsByChild($productobject->getId());
			}
		}
		return $parentIds;
	}

}
	 