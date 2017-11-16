<?php
class Iksula_TrustPilotReviews_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getReviewsCollection($apiKey,$businessId)
	{
		$nextpage_apiurl =Mage::getSingleton('core/session')->getNextPageApiUrl();
		if($nextpage_apiurl){
			$url = $nextpage_apiurl;
		}else{
			$url= Mage::getStoreConfig('trust_pilot_reviews/trust_pilot_reviews_config/review_api_request',Mage::app()->getStore()).'/'.$businessId.'/reviews';
		}
		$headers = array(  
		   "Content-Type: application/json",
		   'apikey: ' . $apiKey
		);

		$rest = curl_init();  
		curl_setopt($rest,CURLOPT_URL,$url); 
		curl_setopt($rest,CURLOPT_HTTPHEADER,$headers); 
		curl_setopt($rest,CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($rest, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($rest);
		$status_code = curl_getinfo($rest, CURLINFO_HTTP_CODE);
		curl_close($rest);
		
		Mage::getSingleton('core/session')->unsNextPageApiUrl();
		if($status_code == 200){
			return json_decode($response,true);
		}
	}

	public function getBusinessId($apiKey)
	{
		$businessName = $this->getBusinessName();
		if($businessName){
			$url= Mage::getStoreConfig('trust_pilot_reviews/trust_pilot_reviews_config/business_id_api_request',Mage::app()->getStore()).'/?name='.$businessName;
			$headers = array(  
			   "Content-Type: application/json",
			   'apikey: ' . $apiKey
			);
			$rest = curl_init();  
			curl_setopt($rest,CURLOPT_URL,$url); 
			curl_setopt($rest,CURLOPT_HTTPHEADER,$headers); 
			curl_setopt($rest,CURLOPT_RETURNTRANSFER, true); 
			curl_setopt($rest, CURLOPT_SSL_VERIFYPEER, false);  
			$response = curl_exec($rest);
			$status_code = curl_getinfo($rest, CURLINFO_HTTP_CODE);
			curl_close($rest);
			if($status_code == 200){
				$result = json_decode($response,true);
				Mage::getConfig()->saveConfig('trust_pilot_reviews/trust_pilot_reviews_config/business_id', $result['id'], 'default', '');
		 		return $result['id'];
			}
		}
	}

	private function getBusinessName(){
		$businessName = Mage::getStoreConfig('trust_pilot_reviews/trust_pilot_reviews_config/business_name',Mage::app()->getStore());
			if($businessName){
				return $businessName;
			}
	}
}