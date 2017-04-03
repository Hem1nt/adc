<?php

class Iksula_Elastic_Model_Indexer_Products_Alldaychemist extends Mage_Index_Model_Indexer_Abstract {

	public function getName() {
		return 'Search Data';
	}

	public function getDescription() {
		return 'Rebuild index of ElasticSearch';
	}

	protected function _registerEvent(Mage_Index_Model_Event $event) {
		// custom register event
		return $this;
	}

	protected function _processEvent(Mage_Index_Model_Event $event) {
		// process index event
	}

	/*
	 *
	 * Event invoked when the Reindex action is performed from System > Index Management
	 * Updates / creates documents of all products in ES
	 *
	 */
	public function reindexAll(){
		//$store = 'statusquo';
		$elasticHelper 	= Mage::helper('elastic');
		$websiteCode	= $elasticHelper->getWebsiteCode();

		$elasticSearch = Mage::getModel('elastic/search');
		$elasticSearch->createAllProductIndexes($websiteCode);
	}

}
