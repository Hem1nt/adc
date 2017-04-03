<?php
class Iksula_Trustedcompany_Helper_Data extends Mage_Core_Helper_Abstract
{
	/*
	*  function to get trusted company dynamic url
	*
	*/
	public function getTrustedReviewLink($order){
		$baseUrl  = Mage::getStoreConfig('trustedcompany/trusted_configuration/trusted_company_url');
		$companyId = Mage::getStoreConfig('trustedcompany/trusted_configuration/company_id');
		$companySecretKey = Mage::getStoreConfig('trustedcompany/trusted_configuration/secrete_key');
		$email_address = $order->getCustomerEmail();
		// $nameofcustomer = $order->getCustomerName();
		$nameofcustomer = $order->getCustomerFirstname();
		$encodedEmail = base64_encode($email_address);
		$customerName = urlencode($nameofcustomer);
		$orderIncrementId = $order->getIncrementId();
		$hashKey = SHA1($companyId.$companySecretKey.$email_address.$orderIncrementId);
		$masterUrl = $baseUrl.'/'.$companyId.'/ul/'.$hashKey.'?a='.$encodedEmail.'&b='.$customerName.'&c='.$orderIncrementId;
		return $masterUrl;
	}

	/*
	* function will call plateform according to user choice
	*/

	public function sendEmail($order){
		$status  = Mage::getStoreConfig('trustedcompany/trusted_configuration/status');
		$plateForm = Mage::getStoreConfig('trustedcompany/trusted_configuration/email_plateform');
		if($status == 1){
			if($plateForm==2){
				$this->sendCheethaEmail($order);
			}else{
				$this->sendMagentoEmail($order);				
			}
		}
	}

	/*
	* send mail using magento plateform
	*/

	public function sendMagentoEmail($order){
		$reviewlink = $this->getTrustedReviewLink($order);
		$templateId = Mage::getStoreConfig('trustedcompany/trusted_configuration/magento_email');
		$senderName = Mage::getStoreConfig('trans_email/ident_support/name');
		$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');		
		$sender = array('name' => $senderName,'email' => $senderEmail);
		$recepientEmail = $order->getCustomerEmail();
		$recepientName = $order->getCustomerName();
		// $recepientName = $order->getCustomerFirstname();
		$store = Mage::app()->getStore()->getId();
		$vars = array(
			'reviewlink' => $reviewlink,
			'customername' => $recepientName
			);
		$translate  = Mage::getSingleton('core/translate');
		Mage::getModel('core/email_template')
		->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
		$translate->setTranslateInline(true);	
	}

	/*
	* send mail using Cheetha Email plateform
	*/

	public function sendCheethaEmail($order){
		$this->sendCheethaAPI($order);
	}


	/*
	* send mail using Cheetha Email API
	*/
	public function sendCheethaAPI($order){

		$login_cheetahmail_curi = Mage::getStoreConfig('trustedcompany/trusted_configuration/cheetah_login_url');
		$login_param_name = Mage::getStoreConfig('trustedcompany/trusted_configuration/cheetah_api_username');
		$login_param_cleartext = Mage::getStoreConfig('trustedcompany/trusted_configuration/cheetah_api_password');
		$login_ebmtrigger_uri = Mage::getStoreConfig('trustedcompany/trusted_configuration/cheetah_api_url');
		$login_aid = Mage::getStoreConfig('trustedcompany/trusted_configuration/cheetah_aid');
		$login_eid = Mage::getStoreConfig('trustedcompany/trusted_configuration/cheetah_eid');//exit;
		$email = $order->getCustomerEmail();
		$customername = $order->getCustomerName();
		$reviewlink = $this->getTrustedReviewLink($order);
		// echo "crulReuest Function: OK<br>";

		$login_uri = $login_cheetahmail_curi;
		$login_params = array(
		"name=".urlencode($login_param_name),
		"cleartext=".urlencode($login_param_cleartext)
		);
		$param_string = implode('&', $login_params);
		$curl = curl_init($login_uri);
		if ($curl){
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $param_string);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_COOKIEJAR, "C:\\my_cookie_file");
			$response = trim(curl_exec($curl));
			if (curl_errno($curl)) {
				echo "error: ".curl_error($curl);
			}
			curl_close($curl);
			// echo "login server response: $response\n<br>";
			if ($response == "OK"){
				$ebmtrigger_uri = $login_ebmtrigger_uri;
				$embtrigger_params = array(
				"aid=".$login_aid,
				"email=".$email,
				"eid=".$login_eid,
				"req=1",
				"CUSTOMERNAME=".$customername,
				"REVIEWLINK=".urlencode($reviewlink),
				);

				// print_r($embtrigger_params);
				$param_string = implode('&', $embtrigger_params);
				$curl = curl_init($ebmtrigger_uri);
				if ($curl){
					curl_setopt($curl, CURLOPT_POST, true);
					curl_setopt($curl, CURLOPT_POSTFIELDS, $param_string);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					//specify location of cookie file
					curl_setopt($curl, CURLOPT_COOKIEFILE, "C:\\my_cookie_file");
					$response = trim(curl_exec($curl));
					//var_dump($response);
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
	 