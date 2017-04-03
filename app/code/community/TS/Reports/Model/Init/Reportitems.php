<?php

class TS_Reports_Model_Init_Reportitems {

	public function initOrderObserve($observer){
		$order = $observer->getEvent()->getOrder();
		Mage::getResourceModel('ts_reports/reportitem')->init($order->getId(), $order->getStoreId());
	}
	
 /********************************************/
 /*			IMPORT PRICES					*/
 /*******************************************/

	public function import($data){
		if(!is_array($data) || empty($data)) return;
		$importItems = $this->checkImportData($data);
		$reports = Mage::getResourceModel('ts_reports/reportitem');
		
		try {
			$results = $reports->import($importItems);
		} catch(Exception $e){
			Mage::logException($e);
			return -1;
		}
		
		$importCatalogCheck = array();
		foreach($importItems as $importItem){
			if($importItem['price_type'] == TS_Reports_Model_Types::REGULAR){
				if(!isset($importCatalogCheck[$importItem['sku']])) $importCatalogCheck[$importItem['sku']] = array();
				$importCatalogCheck[$importItem['sku']][] = $importItem;
			}
		}
		
		try {
			$results += $reports->initRulePrices(null, $importCatalogCheck);
		} catch(Exception $e){
			Mage::logException($e);
			return -1;
		}
		
		return $results;
	}
	
	protected function checkImportData($importItems){
		$types = Mage::getModel('ts_reports/types')->getTypes();
		foreach($importItems as $key => $importItem){
			$unset = false;
			if(!isset($importItems[$key]['categories'])) $importItems[$key]['categories'] = array();
			foreach($importItem['categories'] as $k => $category){
				if(!is_numeric($category)) unset($importItems[$key]['categories'][$k]);
			}
			$importItems[$key]['categories'] = implode('',Mage::helper('ts_reports')->sanitize($importItem['categories']));
			$importItems[$key]['price'] = str_replace(',','.',$importItem['price']);
			
			if(isset($types[$importItem['price_type']])) $importItems[$key]['price_type'] = $types[$importItem['price_type']];
			else $unset = true;
			if(!is_numeric($importItem['price'])) $unset = true;
			if($unset) unset($importItems[$key]);
		}
		return $importItems;
	}
	
	
/*********************************************/
/*  	MASS OVERRIDE from ADMIN CONTROLLER	 */
/*********************************************/

	public function massOverride($ids, $priceType = NULL){
		$count = 0;
		if($priceType == null) $count = Mage::getResourceModel('ts_reports/reportitem')->reset($ids);
		else $count = Mage::getResourceModel('ts_reports/reportitem')->override($ids, $priceType);
		return $count;
	}
	
}