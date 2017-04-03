<?php  

class Iksula_Overrides_Model_Directory_Resource_Country_Collection extends Mage_Directory_Model_Resource_Country_Collection {  
   
	function __construct() {  
		parent::__construct();       
	} 
 
    public function loadByStore($store = null)
    {
	
		$allowCountries = explode(',', (string)Mage::getStoreConfig('general/country/allow', $store));
		
	
		if(Mage::helper('overrides')->isAdmin())
		{
			$additional_allowCountries = explode(',', (string)Mage::getStoreConfig('admin_all_countries/general/countries', $store));
			$combined_allowCountries = array();
			foreach($additional_allowCountries as $country)
			{
				$combined_allowCountries[] = $country;
			}
			
			$combined_allowCountries = array_unique($combined_allowCountries);
			
			if (!empty($combined_allowCountries)) {
				$this->addFieldToFilter("country_id", array('in' => $combined_allowCountries));
			}
			
		} else {
			if (!empty($allowCountries)) {
				$this->addFieldToFilter("country_id", array('in' => $allowCountries));
			}
		}

        return $this;
    }
 
} ?>