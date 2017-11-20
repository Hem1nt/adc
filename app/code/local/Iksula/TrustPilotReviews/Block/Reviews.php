<?php
class Iksula_TrustPilotReviews_Block_Reviews extends Mage_Core_Block_Template{   

	public function getReviews()
	{	
		$businessId = $this->getBusinessId();
		$apiKey = $this->getApiKey();
		if($businessId){
			return Mage::helper('trustpilotreviews')->getReviewsCollection($apiKey,$businessId);
		}else{
			$businessId = Mage::helper('trustpilotreviews')->getBusinessId($apiKey);
			if($businessId){
				return Mage::helper('trustpilotreviews')->getReviewsCollection($apiKey,$businessId);
			}
		}	
	}

	private function getApiKey(){
		return Mage::getStoreConfig('trust_pilot_reviews/trust_pilot_reviews_config/apikey',Mage::app()->getStore());
	}

	private function getBusinessId(){
		return Mage::getStoreConfig('trust_pilot_reviews/trust_pilot_reviews_config/business_id',Mage::app()->getStore());
	}
}