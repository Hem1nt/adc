<?php
/**
 */
class TS_Reports_Helper_Data extends Mage_Core_Helper_Abstract {

	const PATH = 'ts_reports/general/';

	public function getConfigData($configFlag, $store = null){
		if($configFlag){
			$path = self::PATH . $configFlag;
			return Mage::getStoreConfig($path, $store);
		}
		return null;
	}
	
	public function setConfigData($value, $configFlag, $store = null){
		$path = self::PATH . $configFlag;
		if(isset($path)){Mage::log("saving_config $value  $configFlag",null,"conf.log"); return Mage::getModel('core/config')->saveConfig($path, $value);}
		return null;
	}

	public function isEnabled($store = null){
		return $this->getConfigData('enabled', $store);
	}
	
	
	public function getMagentoTimezone($store = null){
		return Mage::app()->getStore($store)->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);	
	}
	
	public function setRefreshDate($date){
		if($this->getConfigData('refresh_date') == null || $date == null){
			$timezone = $this->getMagentoTimezone();
			$this->setConfigData($timezone, 'timezone');
		}
		$this->setConfigData($date, 'refresh_date');
		Mage::getConfig()->cleanCache();
		return $this;
	}
	
	
	public function getRefreshDate(){
		if($this->getMagentoTimezone() != $this->getConfigData('timezone')){
			$this->setRefreshDate(null);
			return null;
		} 	
		return $this->getConfigData('refresh_date');
	}

	public function sanitize(array $categories){
		return array_map(function($entry){ return '/'.$entry.'/'; }, $categories);
	}
	
	public function desanitize($categories){
		return array_filter(preg_split("/\/+/", $categories));
	}
	
	public function getTimezoneOffset($store){
		$timezone = Mage::app()->getStore($store)->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
		$offset = Mage::getModel('core/date')->calculateOffset($timezone);
		return array('timezone' => $timezone, 'offset' => $offset);
	}
}
