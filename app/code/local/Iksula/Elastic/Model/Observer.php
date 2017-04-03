<?php

class Iksula_Elastic_Model_Observer {

	/*
	 *
	 * Observer invoked when a product is saved
	 * Updates / creates product document in ES
	 *
	 */
	public function productSave(Varien_Event_Observer $observer) {

		$productData	= $observer->getEvent()->getProduct();
		/*$storeIds 		= $productData->getStoreIds();
		$websiteCodes 	= $this->getAllWebsiteCodes($storeIds);*/
		// Get Website code and use it as elastic index.
		$elasticHelper 	= Mage::helper('elastic');
		$websiteCode	= $elasticHelper->getWebsiteCode();

		$productId 		= $productData['entity_id'];
		$elasticSearch 	= Mage::getModel('elastic/search')->updateProductIndex($productId,$websiteCode);

	}

	/*
	 *
	 * Observer invoked when a product is deleted
	 * Deletes the product document in ES
	 *
	 */
	public function productDelete(Varien_Event_Observer $observer) {

		$productData 	= $observer->getEvent()->getProduct();
		/*$storeIds 		= $productData->getStoreIds();
		$websiteCodes 	= $this->getAllWebsiteCodes($storeIds);*/
		// Get Website code and use it as elastic index.
		$elasticHelper 	= Mage::helper('elastic');
		$websiteCode	= $elasticHelper->getWebsiteCode();

		$productId 		= $productData['entity_id'];
		$elasticSearch 	= Mage::getModel('elastic/search')->deleteProductIndex($productId,$websiteCode);
	}

	/*
	* @param: All store id's of product.
	* @return: All Website code to which product belongs.
	* All those codes will be utilized
	*/
	public function getAllWebsiteCodes($storeIds){
		$allWebsiteData = array();
		foreach($storeIds as $storeId){
			$store 				= Mage::getModel("core/store")->load($storeId);
			$webId 				= $store['website_id'];
			$websiteDetails 	= Mage::app()->getWebsite($webId);
			$name 				= $websiteDetails->getCode();
			$allWebsiteData[] 	= $name;
		}

		return $allWebsiteData;
	}

}
